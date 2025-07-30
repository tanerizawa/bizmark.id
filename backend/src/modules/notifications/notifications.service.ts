import {
  Injectable,
  Logger,
  BadRequestException,
  InternalServerErrorException,
} from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { ConfigService } from '@nestjs/config';
import * as nodemailer from 'nodemailer';

import { Notification, NotificationType, NotificationStatus } from '../../entities';
import {
  CreateNotificationDto,
  SendEmailDto,
  SendSmsDto,
  NotificationQueryDto,
} from '../../dto/notifications';

@Injectable()
export class NotificationsService {
  private readonly logger = new Logger(NotificationsService.name);
  private emailTransporter: nodemailer.Transporter;

  constructor(
    @InjectRepository(Notification)
    private notificationsRepository: Repository<Notification>,
    private configService: ConfigService,
  ) {
    this.setupEmailTransporter();
  }

  private setupEmailTransporter() {
    this.emailTransporter = nodemailer.createTransport({
      host: this.configService.get('SMTP_HOST'),
      port: this.configService.get('SMTP_PORT'),
      secure: false,
      auth: {
        user: this.configService.get('SMTP_USER'),
        pass: this.configService.get('SMTP_PASS'),
      },
    });
  }

  async create(createNotificationDto: CreateNotificationDto): Promise<Notification> {
    try {
      const notification = this.notificationsRepository.create({
        ...createNotificationDto,
        status: NotificationStatus.PENDING,
        createdAt: new Date(),
      });

      const savedNotification = await this.notificationsRepository.save(notification);

      // Auto-send notification based on type
      await this.processNotification(savedNotification);

      return savedNotification;
    } catch (error) {
      this.logger.error(`Failed to create notification: ${error.message}`, error.stack);
      throw new InternalServerErrorException('Failed to create notification');
    }
  }

  async sendEmail(sendEmailDto: SendEmailDto): Promise<void> {
    try {
      const { to, subject, template, data, tenantId, userId } = sendEmailDto;

      // Generate email content based on template
      const htmlContent = await this.generateEmailContent(template, data);

      const mailOptions = {
        from: this.configService.get('SMTP_FROM') || this.configService.get('SMTP_USER'),
        to,
        subject,
        html: htmlContent,
      };

      await this.emailTransporter.sendMail(mailOptions);

      // Save notification record
      await this.create({
        type: NotificationType.EMAIL,
        recipient: to,
        subject,
        content: htmlContent,
        tenantId,
        userId,
        metadata: { template, data },
      });

      this.logger.log(`Email sent successfully to ${to}`);
    } catch (error) {
      this.logger.error(`Failed to send email: ${error.message}`, error.stack);
      throw new InternalServerErrorException('Failed to send email');
    }
  }

  async sendSms(sendSmsDto: SendSmsDto): Promise<void> {
    // TODO: Implement SMS service integration (Twilio, AWS SNS, etc.)
    this.logger.warn('SMS service not implemented yet');

    // Save notification record as pending
    await this.create({
      type: NotificationType.SMS,
      recipient: sendSmsDto.phone,
      subject: 'SMS Notification',
      content: sendSmsDto.message,
      tenantId: sendSmsDto.tenantId,
      userId: sendSmsDto.userId,
    });
  }

  async sendInAppNotification(notification: CreateNotificationDto): Promise<Notification> {
    return await this.create({
      ...notification,
      type: NotificationType.IN_APP,
    });
  }

  async findAll(
    query: NotificationQueryDto,
    tenantId: string,
  ): Promise<{
    data: Notification[];
    total: number;
    page: number;
    limit: number;
  }> {
    const { page = 1, limit = 10, type, status, userId } = query;
    const skip = (page - 1) * limit;

    const queryBuilder = this.notificationsRepository
      .createQueryBuilder('notification')
      .where('notification.tenantId = :tenantId', { tenantId });

    if (type) {
      queryBuilder.andWhere('notification.type = :type', { type });
    }

    if (status) {
      queryBuilder.andWhere('notification.status = :status', { status });
    }

    if (userId) {
      queryBuilder.andWhere('notification.userId = :userId', { userId });
    }

    const [data, total] = await queryBuilder
      .orderBy('notification.createdAt', 'DESC')
      .skip(skip)
      .take(limit)
      .getManyAndCount();

    return {
      data,
      total,
      page,
      limit,
    };
  }

  async findOne(id: string, tenantId: string): Promise<Notification> {
    const notification = await this.notificationsRepository.findOne({
      where: { id, tenantId },
    });

    if (!notification) {
      throw new BadRequestException('Notification not found');
    }

    return notification;
  }

  async markAsRead(id: string, tenantId: string): Promise<Notification> {
    const notification = await this.findOne(id, tenantId);

    notification.status = NotificationStatus.READ;
    notification.readAt = new Date();

    return await this.notificationsRepository.save(notification);
  }

  async markAsUnread(id: string, tenantId: string): Promise<Notification> {
    const notification = await this.findOne(id, tenantId);

    notification.status = NotificationStatus.DELIVERED;
    notification.readAt = null;

    return await this.notificationsRepository.save(notification);
  }

  async deleteNotification(id: string, tenantId: string): Promise<void> {
    const notification = await this.findOne(id, tenantId);
    await this.notificationsRepository.remove(notification);
  }

  private async processNotification(notification: Notification): Promise<void> {
    try {
      switch (notification.type) {
        case NotificationType.EMAIL:
          // Email already handled in sendEmail method
          break;
        case NotificationType.SMS:
          // SMS processing would go here
          break;
        case NotificationType.IN_APP:
          // Mark as delivered for in-app notifications
          notification.status = NotificationStatus.DELIVERED;
          await this.notificationsRepository.save(notification);
          break;
      }
    } catch (error) {
      this.logger.error(`Failed to process notification ${notification.id}: ${error.message}`);
      notification.status = NotificationStatus.FAILED;
      await this.notificationsRepository.save(notification);
    }
  }

  private async generateEmailContent(template: string, data: any): Promise<string> {
    // Simple template system - in production, use a proper template engine
    const templates = {
      welcome: `
        <h1>Welcome to Bizmark UMKM Platform!</h1>
        <p>Hello ${data.name || 'User'},</p>
        <p>Thank you for joining our platform. We're excited to help you manage your business licenses.</p>
        <p>Best regards,<br>Bizmark Team</p>
      `,
      'license-approved': `
        <h1>License Application Approved</h1>
        <p>Hello ${data.name || 'User'},</p>
        <p>Your license application for <strong>${data.licenseName}</strong> has been approved!</p>
        <p>Application ID: ${data.applicationId}</p>
        <p>Best regards,<br>Bizmark Team</p>
      `,
      'license-rejected': `
        <h1>License Application Update</h1>
        <p>Hello ${data.name || 'User'},</p>
        <p>Your license application for <strong>${data.licenseName}</strong> requires revision.</p>
        <p>Reason: ${data.reason}</p>
        <p>Please log in to your account to make the necessary changes.</p>
        <p>Best regards,<br>Bizmark Team</p>
      `,
      'password-reset': `
        <h1>Password Reset Request</h1>
        <p>Hello ${data.name || 'User'},</p>
        <p>You requested a password reset. Click the link below to reset your password:</p>
        <p><a href="${data.resetLink}">Reset Password</a></p>
        <p>This link will expire in 1 hour.</p>
        <p>If you didn't request this reset, please ignore this email.</p>
        <p>Best regards,<br>Bizmark Team</p>
      `,
    };

    return (
      templates[template] ||
      `
      <h1>Notification</h1>
      <p>${data.message || 'You have a new notification.'}</p>
    `
    );
  }

  // Utility methods for common notification scenarios
  async notifyLicenseStatusChange(
    userId: string,
    tenantId: string,
    licenseName: string,
    status: string,
    userEmail?: string,
  ): Promise<void> {
    const subject = `License Application ${status}`;
    const template = status === 'approved' ? 'license-approved' : 'license-rejected';

    // Send in-app notification
    await this.sendInAppNotification({
      type: NotificationType.IN_APP,
      recipient: userId,
      subject,
      content: `Your license application for ${licenseName} has been ${status}`,
      tenantId,
      userId,
    });

    // Send email if email address is provided
    if (userEmail) {
      await this.sendEmail({
        to: userEmail,
        subject,
        template,
        data: { licenseName, status },
        tenantId,
        userId,
      });
    }
  }

  async notifyWelcome(
    userId: string,
    tenantId: string,
    userEmail: string,
    userName: string,
  ): Promise<void> {
    await this.sendEmail({
      to: userEmail,
      subject: 'Welcome to Bizmark UMKM Platform',
      template: 'welcome',
      data: { name: userName },
      tenantId,
      userId,
    });
  }

  async notifyPasswordReset(
    userEmail: string,
    resetToken: string,
    tenantId: string,
    userId: string,
  ): Promise<void> {
    const resetLink = `${this.configService.get('FRONTEND_URL')}/reset-password?token=${resetToken}`;

    await this.sendEmail({
      to: userEmail,
      subject: 'Password Reset Request',
      template: 'password-reset',
      data: { resetLink },
      tenantId,
      userId,
    });
  }
}
