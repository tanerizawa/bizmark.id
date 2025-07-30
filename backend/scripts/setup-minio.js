#!/usr/bin/env node

/* eslint-env node */
/* eslint-disable @typescript-eslint/no-require-imports */
/* eslint-disable no-console */

const { Client } = require('minio');

async function setupMinIO() {
  const minioClient = new Client({
    endPoint: process.env.MINIO_ENDPOINT || 'localhost',
    port: parseInt(process.env.MINIO_PORT, 10) || 9000,
    useSSL: process.env.MINIO_USE_SSL === 'true',
    accessKey: process.env.MINIO_ACCESS_KEY || 'minioadmin',
    secretKey: process.env.MINIO_SECRET_KEY || 'minioadmin',
  });

  const bucketName = process.env.MINIO_BUCKET_NAME || 'bizmark-files';

  try {
    console.log('🪣 Setting up MinIO buckets...');

    // Check if bucket exists
    const exists = await minioClient.bucketExists(bucketName);
    
    if (!exists) {
      // Create bucket
      await minioClient.makeBucket(bucketName, 'us-east-1');
      console.log(`✅ Created bucket: ${bucketName}`);
    } else {
      console.log(`✅ Bucket already exists: ${bucketName}`);
    }

    // Set bucket policy for public read access to certain paths
    const policy = {
      Version: '2012-10-17',
      Statement: [
        {
          Effect: 'Allow',
          Principal: { AWS: ['*'] },
          Action: ['s3:GetObject'],
          Resource: [`arn:aws:s3:::${bucketName}/public/*`],
        },
      ],
    };

    await minioClient.setBucketPolicy(bucketName, JSON.stringify(policy));
    console.log('✅ Set bucket policy for public access');

    // Create folder structure
    const folders = [
      'documents/',
      'documents/licenses/',
      'documents/applications/',
      'documents/certificates/',
      'public/',
      'public/avatars/',
      'public/logos/',
      'temp/',
    ];

    for (const folder of folders) {
      try {
        await minioClient.putObject(bucketName, folder, Buffer.from(''));
        console.log(`✅ Created folder: ${folder}`);
      } catch (folderError) {
        // Folder might already exist, that's okay
        console.log(`ℹ️  Folder may already exist: ${folder}`);
      }
    }

    console.log('🎉 MinIO setup completed successfully!');
  } catch (error) {
    console.error('❌ MinIO setup failed:', error.message);
    process.exit(1);
  }
}

if (require.main === module) {
  setupMinIO();
}

module.exports = { setupMinIO };
