import dataSource from '../data-source';
import { TenantSeeder } from './tenant.seeder';

async function runSeeders() {
  try {
    console.log('Initializing database connection...');
    await dataSource.initialize();

    console.log('Running TenantSeeder...');
    const tenantSeeder = new TenantSeeder();
    await tenantSeeder.run(dataSource);

    console.log('All seeders completed successfully!');
  } catch (error) {
    console.error('Error running seeders:', error);
  } finally {
    await dataSource.destroy();
  }
}

runSeeders();
