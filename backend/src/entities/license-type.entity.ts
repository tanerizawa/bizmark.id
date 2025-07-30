import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  UpdateDateColumn,
  OneToMany,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { Tenant } from './tenant.entity';
import { License } from './license.entity';

export enum LicenseTypeStatus {
  ACTIVE = 'active',
  INACTIVE = 'inactive',
}

@Entity('license_types')
export class LicenseType {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Column({ type: 'varchar', length: 255 })
  name: string;

  @Column({ type: 'varchar', length: 100, unique: true })
  code: string;

  @Column({ type: 'varchar', length: 100 })
  category: string;

  @Column({ type: 'text', nullable: true })
  description: string;

  @Column({ type: 'jsonb' })
  requiredDocuments: Array<{
    name: string;
    description: string;
    required: boolean;
    fileTypes: string[];
    maxSize: number;
  }>;

  @Column({ type: 'jsonb' })
  formFields: Array<{
    name: string;
    label: string;
    type: string;
    required: boolean;
    options?: string[];
    validation?: Record<string, any>;
  }>;

  @Column({ type: 'jsonb' })
  workflow: Array<{
    step: number;
    name: string;
    description: string;
    requiredRole: string;
    autoApprove?: boolean;
    timeLimit?: number; // dalam hari
  }>;

  @Column({ type: 'int', default: 30 })
  validityPeriod: number; // dalam hari

  @Column({ type: 'decimal', precision: 10, scale: 2, nullable: true })
  fee: number;

  @Column({ type: 'enum', enum: LicenseTypeStatus, default: LicenseTypeStatus.ACTIVE })
  status: LicenseTypeStatus;

  @Column({ type: 'int', default: 0 })
  sortOrder: number;

  @Column({ type: 'uuid', nullable: true })
  tenantId: string; // null untuk global license types

  @CreateDateColumn({ type: 'timestamptz' })
  createdAt: Date;

  @UpdateDateColumn({ type: 'timestamptz' })
  updatedAt: Date;

  // Relationships
  @ManyToOne(() => Tenant)
  @JoinColumn({ name: 'tenantId' })
  tenant: Tenant;

  @OneToMany(() => License, (license) => license.licenseType)
  licenses: License[];
}
