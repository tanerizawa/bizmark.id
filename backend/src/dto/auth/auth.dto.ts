import { IsEmail, IsString, MinLength, IsOptional, IsUUID } from 'class-validator';
import { ApiProperty, ApiPropertyOptional } from '@nestjs/swagger';

export class RegisterDto {
  @ApiProperty({ description: 'Email address', example: 'user@example.com' })
  @IsEmail()
  email: string;

  @ApiProperty({ description: 'Password (minimum 8 characters)', example: 'password123' })
  @IsString()
  @MinLength(8)
  password: string;

  @ApiProperty({ description: 'Full name', example: 'John Doe' })
  @IsString()
  fullName: string;

  @ApiPropertyOptional({ description: 'Phone number', example: '+6281234567890' })
  @IsOptional()
  @IsString()
  phone?: string;

  @ApiPropertyOptional({ description: 'NIK (KTP number)', example: '1234567890123456' })
  @IsOptional()
  @IsString()
  nik?: string;

  @ApiPropertyOptional({ description: 'Tenant ID for multi-tenancy' })
  @IsOptional()
  @IsUUID()
  tenantId?: string;
}

export class LoginDto {
  @ApiProperty({ description: 'Email address', example: 'user@example.com' })
  @IsEmail()
  email: string;

  @ApiProperty({ description: 'Password', example: 'password123' })
  @IsString()
  password: string;

  @ApiPropertyOptional({ description: 'Tenant ID for multi-tenancy' })
  @IsOptional()
  @IsUUID()
  tenantId?: string;
}

export class RefreshTokenDto {
  @ApiProperty({ description: 'Refresh token' })
  @IsString()
  refreshToken: string;
}

export class ForgotPasswordDto {
  @ApiProperty({ description: 'Email address', example: 'user@example.com' })
  @IsEmail()
  email: string;

  @ApiPropertyOptional({ description: 'Tenant ID for multi-tenancy' })
  @IsOptional()
  @IsUUID()
  tenantId?: string;
}

export class ResetPasswordDto {
  @ApiProperty({ description: 'Reset token from email' })
  @IsString()
  token: string;

  @ApiProperty({ description: 'New password (minimum 8 characters)', example: 'newpassword123' })
  @IsString()
  @MinLength(8)
  password: string;
}

export class ChangePasswordDto {
  @ApiProperty({ description: 'Current password', example: 'oldpassword123' })
  @IsString()
  currentPassword: string;

  @ApiProperty({ description: 'New password (minimum 8 characters)', example: 'newpassword123' })
  @IsString()
  @MinLength(8)
  newPassword: string;
}
