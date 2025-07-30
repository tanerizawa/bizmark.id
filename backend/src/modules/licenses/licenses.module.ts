import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';

import { LicensesController } from './licenses.controller';
import { LicensesService } from './licenses.service';
import { License, LicenseType, LicenseWorkflow } from '../../entities';

@Module({
  imports: [TypeOrmModule.forFeature([License, LicenseType, LicenseWorkflow])],
  controllers: [LicensesController],
  providers: [LicensesService],
  exports: [LicensesService],
})
export class LicensesModule {}
