import * as bcrypt from 'bcrypt';
import * as crypto from 'crypto';

export class CryptoHelper {
  static async hashPassword(password: string): Promise<string> {
    const saltRounds = parseInt(process.env.BCRYPT_ROUNDS, 10) || 12;
    return bcrypt.hash(password, saltRounds);
  }

  static async comparePassword(password: string, hash: string): Promise<boolean> {
    return bcrypt.compare(password, hash);
  }

  static generateRandomToken(length: number = 32): string {
    return crypto.randomBytes(length).toString('hex');
  }

  static generateRandomNumber(min: number = 100000, max: number = 999999): number {
    return Math.floor(Math.random() * (max - min + 1)) + min;
  }

  static generateHash(data: string): string {
    return crypto.createHash('sha256').update(data).digest('hex');
  }

  static generateFileHash(buffer: Buffer): string {
    return crypto.createHash('md5').update(buffer).digest('hex');
  }

  static maskEmail(email: string): string {
    const [username, domain] = email.split('@');
    if (username.length <= 2) {
      return `${username[0]}*@${domain}`;
    }
    return `${username.substring(0, 2)}${'*'.repeat(username.length - 2)}@${domain}`;
  }

  static maskPhone(phone: string): string {
    if (phone.length <= 4) {
      return phone;
    }
    const start = phone.substring(0, 3);
    const end = phone.substring(phone.length - 3);
    return `${start}${'*'.repeat(phone.length - 6)}${end}`;
  }
}
