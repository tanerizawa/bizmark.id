import {
  Entity,
  PrimaryGeneratedColumn,
  Column,
  CreateDateColumn,
  ManyToOne,
  JoinColumn,
} from 'typeorm';
import { License } from './license.entity';
import { User } from './user.entity';

export enum WorkflowAction {
  SUBMIT = 'submit',
  REVIEW = 'review',
  APPROVE = 'approve',
  REJECT = 'reject',
  REQUEST_REVISION = 'request_revision',
  REVISE = 'revise',
  EXPIRE = 'expire',
  REVOKE = 'revoke',
}

@Entity('license_workflows')
export class LicenseWorkflow {
  @PrimaryGeneratedColumn('uuid')
  id: string;

  @Column({ type: 'enum', enum: WorkflowAction })
  action: WorkflowAction;

  @Column({ type: 'varchar', length: 100 })
  fromStatus: string;

  @Column({ type: 'varchar', length: 100 })
  toStatus: string;

  @Column({ type: 'text', nullable: true })
  comment: string;

  @Column({ type: 'jsonb', nullable: true })
  metadata: Record<string, any>;

  @Column({ type: 'uuid' })
  licenseId: string;

  @Column({ type: 'uuid' })
  actorId: string; // User yang melakukan action

  @CreateDateColumn({ type: 'timestamptz' })
  createdAt: Date;

  // Relationships
  @ManyToOne(() => License, (license) => license.workflows)
  @JoinColumn({ name: 'licenseId' })
  license: License;

  @ManyToOne(() => User)
  @JoinColumn({ name: 'actorId' })
  actor: User;
}
