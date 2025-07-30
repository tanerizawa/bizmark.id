# Database Setup Guide

## Prerequisites
1. Install PostgreSQL
2. Install Redis
3. Create environment file

## Setup Commands

### 1. Install PostgreSQL (macOS)
```bash
brew install postgresql
brew services start postgresql
```

### 2. Create Database
```bash
createdb bizmark_dev
```

### 3. Create .env file
```bash
cp .env.example .env
```

### 4. Update .env with your database credentials
```
DATABASE_HOST=localhost
DATABASE_PORT=5432
DATABASE_USERNAME=your_username
DATABASE_PASSWORD=your_password
DATABASE_NAME=bizmark_dev
```

### 5. Run Migrations
```bash
npm run migration:run
```

### 6. Run Seeder
```bash
npm run seed:run
```

## Development Commands
- `npm run migration:generate -- src/database/migrations/YourMigration`
- `npm run migration:create -- src/database/migrations/YourMigration`
- `npm run migration:run`
- `npm run migration:revert`
- `npm run seed:run`
