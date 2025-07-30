import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  ManyToOne,
  OneToMany,
  JoinColumn,
} from 'typeorm';
import { Tenant } from './tenant.entity';
import { User } from './user.entity';
import { LicenseType } from './license-type.entity';
import { Document } from './document.entity';
import { LicenseWorkflow } from './license-workflow.entity';

export enum LicenseStatus {
  DRAFT = 'draft',
  SUBMITTED = 'submitted',
  IN_REVIEW = 'in_review',
  REQUIRES_REVISION = 'requires_revision',
  APPROVED = 'approved',
  REJECTED = 'rejected',
  EXPIRED = 'expired',
  REVOKED = 'revoked',
}

@Entity('licenses')
export class License {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Column({ type: 'varchar', length: 100, unique: true })
  licenseNumber: string;

  @Column({ type: 'varchar', length: 255 })
  businessName: string;

  @Column({ type: 'varchar', length: 255 })
  businessAddress: string;

  @Column({ type: 'varchar', length: 100 })
  businessType: string;

  @Column({ type: 'text', nullable: true })
  businessDescription: string;

  @Column({ type: 'enum', enum: LicenseStatus, default: LicenseStatus.DRAFT })
  status: LicenseStatus;

  @Column({ type: 'date', nullable: true })
  issuedAt: Date;

  @Column({ type: 'date', nullable: true })
  expiresAt: Date;

  @Column({ type: 'date', nullable: true })
  validFrom: Date;

  @Column({ type: 'date', nullable: true })
  validUntil: Date;

  @Column({ type: 'timestamptz', nullable: true })
  applicationDate: Date;

  @Column({ type: 'timestamptz', nullable: true })
  approvalDate: Date;

  @Column({ type: 'text', nullable: true })
  reviewerNotes: string;

  @Column({ type: 'jsonb' })
  businessData: Record<string, any>; // Data bisnis spesifik sesuai jenis izin

  @Column({ type: 'jsonb', nullable: true })
  requirements: Record<string, any>; // Requirements checklist

  @Column({ type: 'text', nullable: true })
  notes: string;

  @Column({ type: 'text', nullable: true })
  rejectionReason: string;

  @Column({ type: 'uuid' })
  tenantId: string;

  @Column({ type: 'uuid' })
  applicantId: string;

  @Column({ type: 'uuid' })
  licenseTypeId: string;

  @Column({ type: 'uuid', nullable: true })
  reviewerId: string; // Officer yang me-review

  @CreateDateColumn({ type: 'timestamptz' })
  createdAt: Date;

  @UpdateDateColumn({ type: 'timestamptz' })
  updatedAt: Date;

  // Relationships
  @ManyToOne(() => Tenant, (tenant) => tenant.licenses)
  @JoinColumn({ name: 'tenantId' })
  tenant: Tenant;

  @ManyToOne(() => User, (user) => user.licenses)
  @JoinColumn({ name: 'applicantId' })
  applicant: User;

  @ManyToOne(() => User)
  @JoinColumn({ name: 'reviewerId' })
  reviewer: User;

  @ManyToOne(() => LicenseType, (licenseType) => licenseType.licenses)
  @JoinColumn({ name: 'licenseTypeId' })
  licenseType: LicenseType;

  @OneToMany(() => Document, (document) => document.license)
  documents: Document[];

  @OneToMany(() => LicenseWorkflow, (workflow) => workflow.license)
  workflows: LicenseWorkflow[];
}
