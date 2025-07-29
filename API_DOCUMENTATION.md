# API Documentation - Platform SaaS Perizinan UMKM

## Base URL
- Development: `http://localhost:3001/api`
- Production: `https://api.bizmark.id/api`

## Authentication
Semua endpoint yang memerlukan autentikasi menggunakan Bearer Token (JWT).

```http
Authorization: Bearer <access_token>
```

## Response Format
Semua response menggunakan format JSON konsisten:

```json
{
  "success": true,
  "data": {},
  "message": "Success message",
  "timestamp": "2025-07-29T10:00:00Z"
}
```

Error Response:
```json
{
  "success": false,
  "error": {
    "code": "ERROR_CODE",
    "message": "Error message",
    "details": {}
  },
  "timestamp": "2025-07-29T10:00:00Z"
}
```

## Authentication Endpoints

### POST /auth/register
Registrasi pengguna baru

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "firstName": "John",
  "lastName": "Doe",
  "companyName": "PT Example",
  "role": "client"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": "uuid",
      "email": "user@example.com",
      "firstName": "John",
      "lastName": "Doe",
      "role": "client",
      "tenantId": "uuid"
    },
    "tokens": {
      "accessToken": "jwt_token",
      "refreshToken": "refresh_token",
      "expiresIn": 900
    }
  }
}
```

### POST /auth/login
Login pengguna

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": "uuid",
      "email": "user@example.com",
      "firstName": "John",
      "lastName": "Doe",
      "role": "client",
      "tenantId": "uuid"
    },
    "tokens": {
      "accessToken": "jwt_token",
      "refreshToken": "refresh_token",
      "expiresIn": 900
    }
  }
}
```

### POST /auth/refresh
Refresh access token

**Request Body:**
```json
{
  "refreshToken": "refresh_token"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "accessToken": "new_jwt_token",
    "expiresIn": 900
  }
}
```

### POST /auth/logout
Logout pengguna

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "message": "Successfully logged out"
}
```

## User Management Endpoints

### GET /users/profile
Mendapatkan profil pengguna saat ini

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "email": "user@example.com",
    "firstName": "John",
    "lastName": "Doe",
    "companyName": "PT Example",
    "role": "client",
    "tenantId": "uuid",
    "createdAt": "2025-07-29T10:00:00Z",
    "updatedAt": "2025-07-29T10:00:00Z"
  }
}
```

### PUT /users/profile
Update profil pengguna

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Request Body:**
```json
{
  "firstName": "Jane",
  "lastName": "Doe",
  "companyName": "PT New Example"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "email": "user@example.com",
    "firstName": "Jane",
    "lastName": "Doe",
    "companyName": "PT New Example",
    "role": "client",
    "tenantId": "uuid",
    "updatedAt": "2025-07-29T10:30:00Z"
  }
}
```

### GET /users
Mendapatkan daftar pengguna (Admin only)

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Query Parameters:**
- `page` (optional): Page number (default: 1)
- `limit` (optional): Items per page (default: 10)
- `search` (optional): Search term
- `role` (optional): Filter by role

**Response:**
```json
{
  "success": true,
  "data": {
    "users": [
      {
        "id": "uuid",
        "email": "user@example.com",
        "firstName": "John",
        "lastName": "Doe",
        "role": "client",
        "createdAt": "2025-07-29T10:00:00Z"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 10,
      "total": 50,
      "totalPages": 5
    }
  }
}
```

## License Management Endpoints

### GET /licenses/types
Mendapatkan daftar jenis izin

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": "uuid",
      "name": "Izin Usaha Mikro Kecil",
      "description": "Izin untuk usaha mikro dan kecil",
      "category": "business",
      "requiredDocuments": [
        "KTP",
        "NPWP",
        "Surat Keterangan Domisili"
      ],
      "formSchema": {
        "type": "object",
        "properties": {
          "businessName": {
            "type": "string",
            "required": true
          },
          "businessAddress": {
            "type": "string",
            "required": true
          }
        }
      },
      "processingTime": 14,
      "fee": 100000,
      "isActive": true
    }
  ]
}
```

### POST /licenses/types
Membuat jenis izin baru (Admin only)

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Request Body:**
```json
{
  "name": "Izin Usaha Baru",
  "description": "Deskripsi izin usaha baru",
  "category": "business",
  "requiredDocuments": ["KTP", "NPWP"],
  "formSchema": {
    "type": "object",
    "properties": {
      "businessName": {
        "type": "string",
        "required": true
      }
    }
  },
  "processingTime": 7,
  "fee": 150000
}
```

### GET /licenses/applications
Mendapatkan daftar aplikasi izin

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Query Parameters:**
- `page` (optional): Page number
- `limit` (optional): Items per page
- `status` (optional): Filter by status (pending, approved, rejected)
- `type` (optional): Filter by license type ID

**Response:**
```json
{
  "success": true,
  "data": {
    "applications": [
      {
        "id": "uuid",
        "licenseType": {
          "id": "uuid",
          "name": "Izin Usaha Mikro Kecil"
        },
        "applicationData": {
          "businessName": "Toko ABC",
          "businessAddress": "Jl. Example No. 123"
        },
        "status": "pending",
        "submittedAt": "2025-07-29T10:00:00Z",
        "processedAt": null,
        "notes": null,
        "applicant": {
          "id": "uuid",
          "name": "John Doe"
        }
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 10,
      "total": 25,
      "totalPages": 3
    }
  }
}
```

### POST /licenses/applications
Mengajukan aplikasi izin baru

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Request Body:**
```json
{
  "licenseTypeId": "uuid",
  "applicationData": {
    "businessName": "Toko ABC",
    "businessAddress": "Jl. Example No. 123"
  },
  "documentIds": ["uuid1", "uuid2"]
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "licenseTypeId": "uuid",
    "applicationData": {
      "businessName": "Toko ABC",
      "businessAddress": "Jl. Example No. 123"
    },
    "status": "pending",
    "submittedAt": "2025-07-29T10:00:00Z"
  }
}
```

### GET /licenses/applications/:id
Mendapatkan detail aplikasi izin

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "licenseType": {
      "id": "uuid",
      "name": "Izin Usaha Mikro Kecil",
      "description": "Izin untuk usaha mikro dan kecil"
    },
    "applicationData": {
      "businessName": "Toko ABC",
      "businessAddress": "Jl. Example No. 123"
    },
    "status": "pending",
    "submittedAt": "2025-07-29T10:00:00Z",
    "processedAt": null,
    "notes": null,
    "documents": [
      {
        "id": "uuid",
        "fileName": "ktp.pdf",
        "originalName": "KTP.pdf",
        "mimeType": "application/pdf",
        "size": 1024000
      }
    ],
    "history": [
      {
        "status": "pending",
        "changedAt": "2025-07-29T10:00:00Z",
        "changedBy": {
          "id": "uuid",
          "name": "John Doe"
        },
        "notes": "Application submitted"
      }
    ]
  }
}
```

### PUT /licenses/applications/:id/status
Update status aplikasi izin (Admin/Consultant only)

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Request Body:**
```json
{
  "status": "approved",
  "notes": "Application approved after review"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "status": "approved",
    "processedAt": "2025-07-29T10:30:00Z",
    "notes": "Application approved after review"
  }
}
```

## Document Management Endpoints

### POST /documents/upload
Upload dokumen

**Headers:**
```http
Authorization: Bearer <access_token>
Content-Type: multipart/form-data
```

**Request Body (Form Data):**
- `file`: File to upload
- `category` (optional): Document category
- `description` (optional): Document description

**Response:**
```json
{
  "success": true,
  "data": {
    "id": "uuid",
    "fileName": "tenant-id/user-id/1627564800000-document.pdf",
    "originalName": "document.pdf",
    "mimeType": "application/pdf",
    "size": 1024000,
    "category": "license",
    "description": "KTP untuk aplikasi izin",
    "uploadedAt": "2025-07-29T10:00:00Z",
    "downloadUrl": "/documents/uuid/download"
  }
}
```

### GET /documents
Mendapatkan daftar dokumen

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Query Parameters:**
- `page` (optional): Page number
- `limit` (optional): Items per page
- `category` (optional): Filter by category

**Response:**
```json
{
  "success": true,
  "data": {
    "documents": [
      {
        "id": "uuid",
        "fileName": "document.pdf",
        "originalName": "KTP.pdf",
        "mimeType": "application/pdf",
        "size": 1024000,
        "category": "license",
        "uploadedAt": "2025-07-29T10:00:00Z",
        "downloadUrl": "/documents/uuid/download"
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 10,
      "total": 15,
      "totalPages": 2
    }
  }
}
```

### GET /documents/:id/download
Download dokumen

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
Binary file dengan appropriate headers

### DELETE /documents/:id
Hapus dokumen

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "message": "Document deleted successfully"
}
```

## Reports Endpoints

### GET /reports/financial
Mendapatkan laporan keuangan

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Query Parameters:**
- `startDate`: Start date (YYYY-MM-DD)
- `endDate`: End date (YYYY-MM-DD)
- `type`: Report type (profit-loss, balance-sheet, cash-flow)

**Response:**
```json
{
  "success": true,
  "data": {
    "reportType": "profit-loss",
    "period": {
      "startDate": "2025-01-01",
      "endDate": "2025-07-29"
    },
    "data": {
      "revenue": 50000000,
      "expenses": 30000000,
      "netIncome": 20000000,
      "details": {
        "operatingRevenue": 45000000,
        "nonOperatingRevenue": 5000000,
        "operatingExpenses": 25000000,
        "nonOperatingExpenses": 5000000
      }
    },
    "generatedAt": "2025-07-29T10:00:00Z"
  }
}
```

### GET /reports/tax
Mendapatkan laporan pajak

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Query Parameters:**
- `year`: Tax year
- `type`: Report type (annual, monthly)
- `month` (optional): Month for monthly report

**Response:**
```json
{
  "success": true,
  "data": {
    "reportType": "annual",
    "year": 2025,
    "data": {
      "totalIncome": 60000000,
      "taxableIncome": 50000000,
      "incomeTax": 12500000,
      "vatPaid": 5000000,
      "otherTaxes": 1000000,
      "totalTaxes": 18500000
    },
    "generatedAt": "2025-07-29T10:00:00Z"
  }
}
```

### POST /reports/generate
Generate dan download laporan

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Request Body:**
```json
{
  "type": "financial",
  "subType": "profit-loss",
  "format": "pdf",
  "period": {
    "startDate": "2025-01-01",
    "endDate": "2025-07-29"
  }
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "jobId": "uuid",
    "status": "processing",
    "message": "Report generation started"
  }
}
```

### GET /reports/jobs/:jobId
Cek status generate laporan

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "data": {
    "jobId": "uuid",
    "status": "completed",
    "downloadUrl": "/reports/download/uuid",
    "generatedAt": "2025-07-29T10:05:00Z"
  }
}
```

## Notifications Endpoints

### GET /notifications
Mendapatkan notifikasi

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Query Parameters:**
- `page` (optional): Page number
- `limit` (optional): Items per page
- `unread` (optional): Filter unread notifications (true/false)

**Response:**
```json
{
  "success": true,
  "data": {
    "notifications": [
      {
        "id": "uuid",
        "title": "License Application Approved",
        "message": "Your license application has been approved",
        "type": "license_approved",
        "isRead": false,
        "createdAt": "2025-07-29T10:00:00Z",
        "data": {
          "applicationId": "uuid"
        }
      }
    ],
    "pagination": {
      "page": 1,
      "limit": 10,
      "total": 5,
      "totalPages": 1
    },
    "unreadCount": 3
  }
}
```

### PUT /notifications/:id/read
Tandai notifikasi sebagai sudah dibaca

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "message": "Notification marked as read"
}
```

### PUT /notifications/read-all
Tandai semua notifikasi sebagai sudah dibaca

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "message": "All notifications marked as read"
}
```

## Dashboard Endpoints

### GET /dashboard/stats
Mendapatkan statistik dashboard

**Headers:**
```http
Authorization: Bearer <access_token>
```

**Response:**
```json
{
  "success": true,
  "data": {
    "licenses": {
      "total": 25,
      "pending": 5,
      "approved": 18,
      "rejected": 2
    },
    "documents": {
      "total": 50,
      "thisMonth": 12
    },
    "reports": {
      "generated": 8,
      "thisMonth": 3
    },
    "recentActivity": [
      {
        "id": "uuid",
        "type": "license_submitted",
        "message": "New license application submitted",
        "timestamp": "2025-07-29T09:30:00Z"
      }
    ]
  }
}
```

## Error Codes

| Code | Message | Description |
|------|---------|-------------|
| AUTH_001 | Invalid credentials | Email atau password salah |
| AUTH_002 | Token expired | Access token sudah expired |
| AUTH_003 | Invalid token | Token tidak valid |
| AUTH_004 | Unauthorized | Tidak memiliki akses |
| AUTH_005 | Forbidden | Akses ditolak |
| USER_001 | User not found | User tidak ditemukan |
| USER_002 | Email already exists | Email sudah terdaftar |
| LICENSE_001 | License type not found | Jenis izin tidak ditemukan |
| LICENSE_002 | Application not found | Aplikasi izin tidak ditemukan |
| LICENSE_003 | Invalid status transition | Perubahan status tidak valid |
| DOC_001 | Document not found | Dokumen tidak ditemukan |
| DOC_002 | File too large | File terlalu besar |
| DOC_003 | Invalid file type | Tipe file tidak didukung |
| REPORT_001 | Report generation failed | Gagal generate laporan |
| SYSTEM_001 | Internal server error | Error sistem internal |

## Rate Limiting

API menggunakan rate limiting untuk mencegah abuse:
- **General endpoints**: 100 requests per minute per IP
- **Upload endpoints**: 10 requests per minute per user
- **Report generation**: 5 requests per minute per user

Headers yang dikembalikan:
```http
X-RateLimit-Limit: 100
X-RateLimit-Remaining: 95
X-RateLimit-Reset: 1627564800
```

## Webhooks (Optional)

Untuk integrasi dengan sistem eksternal, API mendukung webhooks untuk event tertentu:

### Events
- `license.application.submitted`
- `license.application.approved`  
- `license.application.rejected`
- `document.uploaded`
- `report.generated`

### Webhook Format
```json
{
  "event": "license.application.approved",
  "data": {
    "applicationId": "uuid",
    "licenseType": "Izin Usaha Mikro Kecil",
    "applicant": {
      "id": "uuid",
      "email": "user@example.com"
    }
  },
  "timestamp": "2025-07-29T10:00:00Z"
}
```
