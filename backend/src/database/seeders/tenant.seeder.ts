import { DataSource } from 'typeorm';
import { Tenant, TenantStatus, TenantType } from '../../entities/tenant.entity';
import { User, UserRole, UserStatus } from '../../entities/user.entity';
import { LicenseType } from '../../entities/license-type.entity';
import * as bcrypt from 'bcrypt';

export class TenantSeeder {
  public async run(dataSource: DataSource): Promise<void> {
    const tenantRepository = dataSource.getRepository(Tenant);
    const userRepository = dataSource.getRepository(User);
    const licenseTypeRepository = dataSource.getRepository(LicenseType);

    // Create default tenant
    const tenant = new Tenant();
    tenant.name = 'Bizmark Demo';
    tenant.code = 'DEMO';
    tenant.type = TenantType.KOTA;
    tenant.region = 'Jakarta Pusat';
    tenant.address = 'Jl. Medan Merdeka Selatan No. 1';
    tenant.phone = '+62211234567';
    tenant.email = 'demo@bizmark.id';
    tenant.website = 'https://demo.bizmark.id';
    tenant.status = TenantStatus.ACTIVE;
    tenant.settings = {
      theme: 'default',
      timezone: 'Asia/Jakarta',
      language: 'id',
      features: {
        notifications: true,
        reports: true,
        analytics: true,
      },
    };

    const savedTenant = await tenantRepository.save(tenant);

    // Create admin user
    const adminUser = new User();
    adminUser.email = 'admin@bizmark.id';
    adminUser.fullName = 'System Administrator';
    adminUser.password = await bcrypt.hash('admin123', 10);
    adminUser.role = UserRole.TENANT_ADMIN;
    adminUser.status = UserStatus.ACTIVE;
    adminUser.tenant = savedTenant;
    adminUser.phone = '+62211234567';
    adminUser.nik = '3171012345678901';

    await userRepository.save(adminUser);

    // Create sample license types
    const licenseTypes = [
      {
        name: 'Surat Izin Usaha Perdagangan (SIUP)',
        code: 'SIUP',
        category: 'Perdagangan',
        description: 'Izin untuk menjalankan usaha perdagangan',
        requiredDocuments: [
          {
            name: 'KTP Pemilik',
            description: 'Kartu Tanda Penduduk pemilik usaha',
            required: true,
            fileTypes: ['jpg', 'jpeg', 'png', 'pdf'],
            maxSize: 2048000,
          },
          {
            name: 'NPWP',
            description: 'Nomor Pokok Wajib Pajak',
            required: true,
            fileTypes: ['jpg', 'jpeg', 'png', 'pdf'],
            maxSize: 2048000,
          },
        ],
        formFields: [
          {
            name: 'businessName',
            label: 'Nama Usaha',
            type: 'text',
            required: true,
          },
          {
            name: 'businessAddress',
            label: 'Alamat Usaha',
            type: 'textarea',
            required: true,
          },
        ],
        workflow: [
          {
            step: 1,
            name: 'Verifikasi Dokumen',
            description: 'Verifikasi kelengkapan dan keabsahan dokumen',
            requiredRole: 'OFFICER',
            timeLimit: 3,
          },
          {
            step: 2,
            name: 'Persetujuan',
            description: 'Persetujuan dari kepala dinas',
            requiredRole: 'TENANT_ADMIN',
            timeLimit: 4,
          },
        ],
        validityPeriod: 1825, // 5 tahun
        fee: 500000,
        status: 'ACTIVE',
        sortOrder: 1,
        tenantId: savedTenant.id,
      },
      {
        name: 'Tanda Daftar Perusahaan (TDP)',
        code: 'TDP',
        category: 'Perusahaan',
        description: 'Pendaftaran perusahaan di dinas perindustrian dan perdagangan',
        requiredDocuments: [
          {
            name: 'Akta Pendirian',
            description: 'Akta pendirian perusahaan',
            required: true,
            fileTypes: ['pdf'],
            maxSize: 5120000,
          },
          {
            name: 'NPWP Perusahaan',
            description: 'NPWP atas nama perusahaan',
            required: true,
            fileTypes: ['jpg', 'jpeg', 'png', 'pdf'],
            maxSize: 2048000,
          },
        ],
        formFields: [
          {
            name: 'companyName',
            label: 'Nama Perusahaan',
            type: 'text',
            required: true,
          },
          {
            name: 'companyType',
            label: 'Jenis Perusahaan',
            type: 'select',
            required: true,
            options: ['PT', 'CV', 'UD', 'PD'],
          },
        ],
        workflow: [
          {
            step: 1,
            name: 'Verifikasi Dokumen',
            description: 'Verifikasi kelengkapan dokumen perusahaan',
            requiredRole: 'OFFICER',
            timeLimit: 5,
          },
          {
            step: 2,
            name: 'Persetujuan',
            description: 'Persetujuan pendaftaran perusahaan',
            requiredRole: 'TENANT_ADMIN',
            timeLimit: 9,
          },
        ],
        validityPeriod: 1825, // 5 tahun
        fee: 750000,
        status: 'ACTIVE',
        sortOrder: 2,
        tenantId: savedTenant.id,
      },
    ];

    for (const licenseTypeData of licenseTypes) {
      const licenseType = new LicenseType();
      Object.assign(licenseType, licenseTypeData);
      await licenseTypeRepository.save(licenseType);
    }

    console.log('Tenant seeder completed successfully');
  }
}
