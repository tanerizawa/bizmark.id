import {
  Controller,
  Get,
  Post,
  Body,
  Patch,
  Param,
  Delete,
  Query,
  UseGuards,
  ParseUUIDPipe,
  HttpStatus,
} from '@nestjs/common';
import { ApiTags, ApiOperation, ApiResponse, ApiBearerAuth, ApiQuery } from '@nestjs/swagger';

import { NotificationsService } from './notifications.service';
import {
  CreateNotificationDto,
  SendEmailDto,
  SendSmsDto,
  NotificationQueryDto,
  NotificationResponseDto,
  PaginatedNotificationsResponseDto,
  BulkSendEmailDto,
} from '../../dto/notifications';
import { JwtAuthGuard } from '../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../common/guards/roles.guard';
import { TenantGuard } from '../../common/guards/tenant.guard';
import { Roles } from '../../common/decorators/roles.decorator';
import { GetCurrentUser as GetUser } from '../../common/decorators/user.decorator';
import { GetTenant } from '../../common/decorators/tenant.decorator';
import { UserRole } from '../../entities';

@ApiTags('notifications')
@ApiBearerAuth()
@Controller('notifications')
@UseGuards(JwtAuthGuard, TenantGuard, RolesGuard)
export class NotificationsController {
  constructor(private readonly notificationsService: NotificationsService) {}

  @Post()
  @ApiOperation({ summary: 'Create a new notification' })
  @ApiResponse({
    status: HttpStatus.CREATED,
    description: 'Notification created successfully',
    type: NotificationResponseDto,
  })
  @Roles(UserRole.TENANT_ADMIN, UserRole.OFFICER)
  async create(
    @Body() createNotificationDto: CreateNotificationDto,
    @GetTenant() tenantId: string,
  ): Promise<NotificationResponseDto> {
    return await this.notificationsService.create({
      ...createNotificationDto,
      tenantId,
    });
  }

  @Post('send-email')
  @ApiOperation({ summary: 'Send email notification' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Email sent successfully',
  })
  @Roles(UserRole.TENANT_ADMIN, UserRole.OFFICER)
  async sendEmail(
    @Body() sendEmailDto: SendEmailDto,
    @GetTenant() tenantId: string,
    @GetUser('id') userId: string,
  ): Promise<{ message: string }> {
    await this.notificationsService.sendEmail({
      ...sendEmailDto,
      tenantId,
      userId,
    });

    return { message: 'Email sent successfully' };
  }

  @Post('send-sms')
  @ApiOperation({ summary: 'Send SMS notification' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'SMS sent successfully',
  })
  @Roles(UserRole.TENANT_ADMIN, UserRole.OFFICER)
  async sendSms(
    @Body() sendSmsDto: SendSmsDto,
    @GetTenant() tenantId: string,
    @GetUser('id') userId: string,
  ): Promise<{ message: string }> {
    await this.notificationsService.sendSms({
      ...sendSmsDto,
      tenantId,
      userId,
    });

    return { message: 'SMS sent successfully' };
  }

  @Post('bulk-email')
  @ApiOperation({ summary: 'Send bulk email notifications' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Bulk emails sent successfully',
  })
  @Roles(UserRole.TENANT_ADMIN)
  async sendBulkEmail(
    @Body() bulkSendEmailDto: BulkSendEmailDto,
    @GetTenant() tenantId: string,
    @GetUser('id') userId: string,
  ): Promise<{ message: string; sent: number }> {
    const { recipients, ...emailData } = bulkSendEmailDto;
    let sentCount = 0;

    for (const recipient of recipients) {
      try {
        await this.notificationsService.sendEmail({
          ...emailData,
          to: recipient,
          tenantId,
          userId,
        });
        sentCount++;
      } catch (error) {
        // Log error but continue with other recipients
        console.error(`Failed to send email to ${recipient}:`, error.message);
      }
    }

    return {
      message: `Bulk emails processed`,
      sent: sentCount,
    };
  }

  @Get()
  @ApiOperation({ summary: 'Get all notifications' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Notifications retrieved successfully',
    type: PaginatedNotificationsResponseDto,
  })
  @ApiQuery({ name: 'page', required: false, type: Number })
  @ApiQuery({ name: 'limit', required: false, type: Number })
  @ApiQuery({ name: 'type', required: false, enum: ['email', 'sms', 'in_app', 'push'] })
  @ApiQuery({
    name: 'status',
    required: false,
    enum: ['pending', 'sent', 'delivered', 'read', 'failed'],
  })
  @ApiQuery({ name: 'userId', required: false, type: String })
  async findAll(
    @Query() query: NotificationQueryDto,
    @GetTenant() tenantId: string,
    @GetUser('role') userRole: UserRole,
    @GetUser('id') currentUserId: string,
  ): Promise<PaginatedNotificationsResponseDto> {
    // If user is not admin, only show their own notifications
    if (userRole !== UserRole.TENANT_ADMIN && userRole !== UserRole.OFFICER) {
      query.userId = currentUserId;
    }

    const result = await this.notificationsService.findAll(query, tenantId);

    return {
      ...result,
      totalPages: Math.ceil(result.total / result.limit),
    };
  }

  @Get('unread-count')
  @ApiOperation({ summary: 'Get unread notification count for current user' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Unread count retrieved successfully',
  })
  async getUnreadCount(
    @GetTenant() tenantId: string,
    @GetUser('id') userId: string,
  ): Promise<{ count: number }> {
    const result = await this.notificationsService.findAll(
      {
        userId,
        status: 'delivered' as any, // In-app notifications that haven't been read
        limit: 1,
      },
      tenantId,
    );

    return { count: result.total };
  }

  @Get(':id')
  @ApiOperation({ summary: 'Get notification by ID' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Notification retrieved successfully',
    type: NotificationResponseDto,
  })
  async findOne(
    @Param('id', ParseUUIDPipe) id: string,
    @GetTenant() tenantId: string,
    @GetUser('role') userRole: UserRole,
    @GetUser('id') currentUserId: string,
  ): Promise<NotificationResponseDto> {
    const notification = await this.notificationsService.findOne(id, tenantId);

    // Check if user has permission to view this notification
    if (
      userRole !== UserRole.TENANT_ADMIN &&
      userRole !== UserRole.OFFICER &&
      notification.userId !== currentUserId
    ) {
      throw new Error('Access denied');
    }

    return notification;
  }

  @Patch(':id/read')
  @ApiOperation({ summary: 'Mark notification as read' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Notification marked as read',
    type: NotificationResponseDto,
  })
  async markAsRead(
    @Param('id', ParseUUIDPipe) id: string,
    @GetTenant() tenantId: string,
    @GetUser('role') userRole: UserRole,
    @GetUser('id') currentUserId: string,
  ): Promise<NotificationResponseDto> {
    const notification = await this.notificationsService.findOne(id, tenantId);

    // Check if user has permission to mark this notification as read
    if (
      userRole !== UserRole.TENANT_ADMIN &&
      userRole !== UserRole.OFFICER &&
      notification.userId !== currentUserId
    ) {
      throw new Error('Access denied');
    }

    return await this.notificationsService.markAsRead(id, tenantId);
  }

  @Patch(':id/unread')
  @ApiOperation({ summary: 'Mark notification as unread' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Notification marked as unread',
    type: NotificationResponseDto,
  })
  async markAsUnread(
    @Param('id', ParseUUIDPipe) id: string,
    @GetTenant() tenantId: string,
    @GetUser('role') userRole: UserRole,
    @GetUser('id') currentUserId: string,
  ): Promise<NotificationResponseDto> {
    const notification = await this.notificationsService.findOne(id, tenantId);

    // Check if user has permission to mark this notification as unread
    if (
      userRole !== UserRole.TENANT_ADMIN &&
      userRole !== UserRole.OFFICER &&
      notification.userId !== currentUserId
    ) {
      throw new Error('Access denied');
    }

    return await this.notificationsService.markAsUnread(id, tenantId);
  }

  @Delete(':id')
  @ApiOperation({ summary: 'Delete notification' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Notification deleted successfully',
  })
  @Roles(UserRole.TENANT_ADMIN, UserRole.OFFICER)
  async remove(
    @Param('id', ParseUUIDPipe) id: string,
    @GetTenant() tenantId: string,
  ): Promise<{ message: string }> {
    await this.notificationsService.deleteNotification(id, tenantId);
    return { message: 'Notification deleted successfully' };
  }

  @Post('test-email')
  @ApiOperation({ summary: 'Send test email (for development)' })
  @ApiResponse({
    status: HttpStatus.OK,
    description: 'Test email sent successfully',
  })
  @Roles(UserRole.TENANT_ADMIN)
  async sendTestEmail(
    @GetTenant() tenantId: string,
    @GetUser('id') userId: string,
    @GetUser('email') userEmail: string,
  ): Promise<{ message: string }> {
    await this.notificationsService.sendEmail({
      to: userEmail,
      subject: 'Test Email from Bizmark Platform',
      template: 'welcome', // Use existing template
      data: { name: 'Test User' },
      tenantId,
      userId,
    });

    return { message: 'Test email sent successfully' };
  }
}
