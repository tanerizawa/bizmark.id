# 🎯 Rencana Lanjutan Migrasi Monolith → Microservices
> **Tanggal:** 3 Juni 2026  
> **Status Saat Ini:** 42% selesai (37/145 checklist items)  
> **Target:** Production-ready dalam 8-12 minggu

---

## 📊 Executive Summary

### Status Infrastruktur
✅ **19 containers running** di `/home/bizmark/bizmark-microservices/`
- 8 microservices (Auth, AI, Content/SEO, HRM, Email, Perizinan, Proyek, Finansial)
- 5 queue workers + 1 scheduler
- 3 infrastruktur (PostgreSQL, Redis, Traefik)
- 1 storage (MinIO)

⚠️ **Perizinan service UNHEALTHY** - perlu investigasi prioritas
✅ **Semua service lain healthy** (uptime 3-4 hari)

### Progress Per Service
| Service | Infra | Data | Logic | Tests | Auth | Overall |
|---------|:-----:|:----:|:-----:|:-----:|:----:|:-------:|
| Auth | ✅ | 🟡 | ✅ | ❌ | ✅ | **70%** |
| AI (Python) | ✅ | N/A | 🟡 | ❌ | ❌ | **55%** |
| Content/SEO | ✅ | ✅ | ✅ | ✅ | ❌ | **75%** |
| HRM | ✅ | 🟡 | ✅ | ✅ | ❌ | **70%** |
| Email | ✅ | 🟡 | 🟡 | ✅ | ❌ | **40%** |
| Perizinan | ⚠️ | ❌ | 🟡 | 🟡 | ❌ | **35%** |
| Proyek | ✅ | 🟡 | ✅ | ✅ | ❌ | **45%** |
| Finansial | ✅ | ❌ | 🟡 | 🟡 | ❌ | **30%** |

**Legend:** ✅ Complete | 🟡 Partial | ❌ Not Started | ⚠️ Issues

---

## 🔴 BLOCKER — Harus Diselesaikan Segera

### B1. Perizinan Service Unhealthy (CRITICAL)
**Impact:** Core business domain tidak bisa diakses via microservices

**Root Cause Analysis:**
```bash
# Investigasi logs
docker logs ms_perizinan_service --tail 100

# Cek health endpoint
curl http://localhost:8005/health

# Cek database connection
docker exec ms_perizinan_service php artisan migrate:status
```

**Action Items:**
- [ ] Diagnose health check failure (database connection? migration issues?)
- [ ] Review recent changes di perizinan-service
- [ ] Fix blocking issues
- [ ] Restart service dan monitor stability
- [ ] Add alerting untuk prevent future unhealthy state

**Timeline:** 1-2 hari  
**Owner:** DevOps + Backend Team

---

### B2. Data Sync dari Monolith (BLOCKER untuk Testing)
**Status:** ❌ Hampir semua data production masih di monolith

**Current State:**
- ✅ 163 articles synced (Content/SEO)
- ✅ 447 article_topics synced
- ✅ 30 KBLI sample (perlu 1,793 full data)
- ❌ 0 users synced (critical untuk testing auth flow)
- ❌ 0 permit applications
- ❌ 0 invoices, projects, clients, leads

**Migration Priority Order:**
```
WEEK 1-2: Critical Business Data
├── 1. Users (auth) → bizmark_auth_dev
├── 2. Clients → bizmark_perizinan_dev (CRM data)
├── 3. KBLI (1,793 full) → bizmark_perizinan_dev
├── 4. PermitTypes + Templates → bizmark_perizinan_dev
└── 5. Master data (expense_categories, payment_methods, tax_rates)

WEEK 3: Transactional Data (Read-Only untuk Testing)
├── 6. PermitApplications (top 100 recent)
├── 7. Projects (top 50 active)
├── 8. Invoices (last 6 months)
└── 9. Service inquiries + leads

WEEK 4: Historical Data (Incremental)
├── 10. Job vacancies + applications
├── 11. Email campaigns + templates
└── 12. Full dataset migration
```

**Script Execution Plan:**
```bash
# Phase 1: Enhanced sync script
cd /home/bizmark/bizmark.id

# Update sync script dengan semua tabel prioritas
nano sync_data_to_microservices.sh

# Test sync dengan dry-run mode
./sync_data_to_microservices.sh --dry-run

# Execute actual sync
./sync_data_to_microservices.sh

# Validation report
docker exec ms_postgres psql -U bizmark_ms -c "
SELECT 
  'auth' as service, 
  (SELECT COUNT(*) FROM bizmark_auth_dev.users) as users,
  (SELECT COUNT(*) FROM bizmark_perizinan_dev.kblis) as kblis,
  (SELECT COUNT(*) FROM bizmark_perizinan_dev.clients) as clients;
"
```

**Critical Blockers:**
1. **KBLI Schema Issue** — `title NOT NULL` constraint blocks 1,793 records import
   ```sql
   -- Fix di perizinan-service
   docker exec ms_postgres psql -U bizmark_ms -d bizmark_perizinan_dev -c "
   ALTER TABLE kblis ALTER COLUMN title DROP NOT NULL;
   -- atau
   UPDATE kblis SET title = code WHERE title IS NULL;
   "
   ```

2. **Foreign Key Cross-Domain** — Beberapa migration masih reference tabel di monolith
   - Review semua migrations di perizinan, proyek, finansial
   - Remove atau soft-delete FK ke users, clients jika belum di-sync

**Timeline:** 2 minggu  
**Owner:** Data Engineering + Backend

---

### B3. Auth Layer Integration (SECURITY BLOCKER)
**Status:** ✅ Middleware created, ❌ Not fully tested across services

**Current Gap:**
- JWT middleware exists in all 6 Laravel services
- Traefik forward auth configured
- No integration tests for auth flow
- No service-to-service authentication

**Action Items:**

**Week 1: Auth Testing & Hardening**
```bash
# 1. Test auth flow di setiap service
for svc in content hrm email perizinan proyek finansial; do
  echo "Testing $svc..."
  
  # Without token → expect 401
  curl -X GET http://localhost:8090/ms/$svc/v1/test -w "\n%{http_code}\n"
  
  # With valid token → expect 200
  TOKEN=$(curl -X POST http://localhost:8000/auth/login \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@bizmark.id","password":"password"}' \
    | jq -r '.token')
  
  curl -X GET http://localhost:8090/ms/$svc/v1/test \
    -H "Authorization: Bearer $TOKEN" \
    -w "\n%{http_code}\n"
done
```

**Week 2: Service-to-Service Auth**
```php
// Implement di shared middleware
// /home/bizmark/bizmark-microservices/shared/middleware/InternalServiceAuth.php

class InternalServiceMiddleware {
    public function handle($request, Closure $next) {
        $internalSecret = $request->header('X-Internal-Secret');
        
        if ($internalSecret === env('MS_INTERNAL_SECRET')) {
            // Bypass JWT validation untuk inter-service calls
            $request->merge(['internal_call' => true]);
            return $next($request);
        }
        
        // Continue dengan JWT validation
        return app(JwtMiddleware::class)->handle($request, $next);
    }
}
```

**Implementation Checklist:**
- [ ] Add integration test suite untuk auth flows
- [ ] Test 401 responses tanpa token
- [ ] Test 403 responses dengan wrong role
- [ ] Implement internal service bypass
- [ ] Document service-to-service call patterns
- [ ] Add auth metrics ke health endpoints

**Timeline:** 2 minggu  
**Owner:** Security + Backend Team

---

## 🟡 P1 — Critical Path (Production Blockers)

### P1.1 CRM Service/Module Decision
**Status:** ❌ Client, ServiceInquiry, ServiceCostRequest models homeless

**Options Analysis:**

**Option A: Standalone CRM Service** ⭐ RECOMMENDED
```
Pros:
+ Clear domain boundary
+ Dapat scale independent
+ Reusable untuk future products
+ Follows true microservices pattern

Cons:
- Extra service to maintain
- Network hop untuk perizinan → CRM calls
- Butuh 1-2 minggu development
```

**Option B: Embed di Perizinan Service**
```
Pros:
+ Faster implementation (3-5 hari)
+ No network latency
+ Simplified deployment

Cons:
- Violates single responsibility
- CRM logic tercampur dengan perizinan
- Sulit di-extract nanti
```

**Decision Matrix:**
| Kriteria | CRM Standalone | Embed Perizinan | Weight |
|----------|:--------------:|:---------------:|:------:|
| Domain Purity | ⭐⭐⭐⭐⭐ | ⭐⭐ | 5 |
| Development Speed | ⭐⭐ | ⭐⭐⭐⭐⭐ | 3 |
| Scalability | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | 4 |
| Maintenance | ⭐⭐⭐⭐ | ⭐⭐ | 5 |
| **Total Score** | **77/100** | **59/100** | - |

**RECOMMENDATION: Build standalone CRM Service**

**Implementation Plan (2 weeks):**
```bash
# Week 1: Scaffold & Core Models
cd /home/bizmark/bizmark-microservices/services
cp -r perizinan-service crm-service
cd crm-service

# Clean up, keep only:
# - Client model + migration
# - ServiceInquiry model + migration  
# - ServiceCostRequest model + migration
# - LeadManagementService
# - ClientController, LeadController

# Week 2: API Endpoints & Integration
# - POST /v1/clients
# - GET /v1/clients/{id}
# - POST /v1/leads (service inquiries)
# - POST /v1/leads/{id}/analyze → proxy to AI service
# - POST /v1/leads/{id}/cost-requests
# - Integration testing dengan perizinan service
```

**Timeline:** 2 minggu  
**Owner:** Backend Team Lead

---

### P1.2 Email Service SMTP Configuration
**Status:** 🟡 Configured tapi belum tested actual delivery

**Current State:**
```env
# .env exists di email-service
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com  # atau internal SMTP
MAIL_PORT=587
MAIL_USERNAME=<configured?>
MAIL_PASSWORD=<configured?>
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bizmark.id
```

**Testing Plan:**
```bash
# 1. Verify SMTP credentials
docker exec ms_email_service php artisan tinker
>>> Mail::raw('Test email', fn($m) => $m->to('test@example.com')->subject('Test'));

# 2. Test SendEmailCampaignJob
docker exec ms_email_service php artisan queue:work --once

# 3. Monitor mail logs
docker logs ms_email_worker --tail 50 -f
```

**Action Items:**
- [ ] Verify SMTP credentials aktif
- [ ] Test email delivery ke test addresses
- [ ] Configure email queue monitoring
- [ ] Add retry logic untuk failed emails (done via Laravel queue)
- [ ] Implement email bounce handling
- [ ] Add email analytics (open rate, click rate)

**Timeline:** 1 minggu  
**Owner:** DevOps + Backend

---

### P1.3 Finansial Service Completion
**Status:** ❌ Business logic 30% complete

**Missing Critical Features:**

**1. Midtrans Integration Testing**
```php
// Test payment flow end-to-end
POST /v1/invoices/{id}/payment-link
→ Create Midtrans transaction
→ Return payment URL
→ Client pays
→ Webhook callback
→ Update invoice status

// Currently: MidtransService created, webhook endpoint ada, belum tested
```

**Action Items:**
- [ ] Setup Midtrans sandbox account
- [ ] Test create transaction API
- [ ] Test webhook signature verification
- [ ] Implement webhook endpoint fully
  ```php
  POST /webhook/midtrans (no auth, signature verification)
  → Verify signature
  → Update invoice status
  → Create payment record
  → Trigger notification
  ```
- [ ] Add idempotency untuk prevent double payment
- [ ] Test refund flow

**2. Cash Account Balance Calculations**
```bash
# Command exists, needs testing
docker exec ms_finansial_service php artisan finansial:recalculate-balances

# Verify:
# - Balance = SUM(credits) - SUM(debits)
# - Balance history accurate
# - No race conditions
```

**3. Bank Reconciliation Logic**
```php
// Auto-match algorithm
POST /v1/bank-reconciliations
→ Parse bank statement (CSV/PDF)
→ Match transactions dengan invoices/expenses
→ Flag unmatched transactions
→ Manual review endpoint
```

**Timeline:** 3 minggu  
**Owner:** Finance Domain Expert + Backend

---

### P1.4 Perizinan Service Completion
**Status:** 🟡 35% complete, UNHEALTHY

**Critical Missing Features:**

**1. PermitApplication State Machine** ✅ Done (according to checklist)
**2. AI Integration untuk Document Analysis**
```php
// PerizinanAIService ported, needs testing
POST /v1/permit-applications/{id}/analyze
→ Extract business description
→ Call AI service for KBLI recommendations
→ Generate compliance checklist
→ Return analysis result
```

**3. OSS Integration via Civic Stack**
```php
// civic_stack is Python service running separately?
// Need API client in perizinan-service

POST /v1/permit-applications/{id}/oss-status
→ Call civic_stack API
→ Check OSS permit status
→ Update OssPermitStatus model
→ Notify client if status changed
```

**4. Document Upload & Management**
```php
// Use MinIO (already running)
POST /v1/permit-applications/{id}/documents
→ Validate file type (PDF, image)
→ Upload to MinIO
→ Create ApplicationDocument record
→ OCR extraction (future)
```

**Action Items:**
- [ ] Fix unhealthy status
- [ ] Complete AI integration testing
- [ ] Setup civic_stack API client
- [ ] Configure MinIO storage untuk documents
- [ ] Add document preview endpoint
- [ ] Implement document verification workflow

**Timeline:** 3 minggu  
**Owner:** Backend + Integration Team

---

## 🟢 P2 — Important (Post-Beta)

### P2.1 Observability & Monitoring
**Status:** ❌ Zero production monitoring

**Required Components:**

**1. Centralized Logging**
```yaml
# Add to docker-compose.dev.yml
services:
  loki:
    image: grafana/loki:latest
    ports:
      - "3100:3100"
    
  promtail:
    image: grafana/promtail:latest
    volumes:
      - /var/lib/docker/containers:/var/lib/docker/containers:ro
      - ./promtail-config.yml:/etc/promtail/config.yml
    
  grafana:
    image: grafana/grafana:latest
    ports:
      - "3000:3000"
    environment:
      - GF_SECURITY_ADMIN_PASSWORD=admin
```

**2. Metrics Collection**
```bash
# Install Laravel Pulse di critical services
cd services/perizinan-service
composer require laravel/pulse

# Configure
php artisan vendor:publish --provider="Laravel\Pulse\PulseServiceProvider"

# Access at /pulse dashboard
```

**3. Error Tracking (Sentry)**
```bash
# Already in checklist, needs execution
for svc in auth-service content-seo-service hrm-service email-service perizinan-service proyek-service finansial-service; do
  cd services/$svc
  composer require sentry/sentry-laravel
  php artisan sentry:publish
  cd ../..
done

# Add SENTRY_LARAVEL_DSN to .env.dev
```

**4. Uptime Monitoring**
```bash
# Simple health check cron
*/5 * * * * /home/bizmark/bizmark-microservices/scripts/health_check.sh
```

**Timeline:** 2 minggu  
**Owner:** DevOps

---

### P2.2 Performance Optimization

**1. Database Query Optimization**
```bash
# Enable query logging
docker exec ms_perizinan_service php artisan telescope:install

# Identify N+1 queries
# Add eager loading where needed
```

**2. Caching Strategy**
```php
// Implement di setiap service
// Redis cache untuk frequently accessed data

// Example: KBLI search results
Cache::remember("kbli:search:{$query}", 3600, function() use ($query) {
    return Kbli::search($query)->get();
});
```

**3. Queue Worker Tuning**
```yaml
# docker-compose.dev.yml
# Increase workers untuk high-traffic queues
ms_perizinan_worker:
  command: php artisan queue:work --queue=default,ai-analysis --tries=3 --sleep=3
  deploy:
    replicas: 3  # Scale to 3 workers
```

**Timeline:** Ongoing optimization  
**Owner:** Performance Team

---

### P2.3 Testing Completion

**Current Test Coverage:**
- Content/SEO: 9/9 ✅
- HRM: 8/8 ✅
- Email: 9/9 ✅
- Perizinan: 7/8 🟡
- Proyek: 6/7 🟡
- Finansial: 8/9 🟡

**Target: 100% passing + 60% coverage**

**Test Categories Needed:**

**1. Unit Tests**
```php
// Every Service class needs tests
tests/Unit/Services/InvoiceServiceTest.php
tests/Unit/Services/ProjectServiceTest.php
tests/Unit/Services/PermitApplicationServiceTest.php
```

**2. Integration Tests**
```php
// Cross-service communication
tests/Integration/PerizinanToAIServiceTest.php
tests/Integration/FinansialToEmailServiceTest.php
tests/Integration/AuthFlowTest.php
```

**3. E2E Tests (Via Traefik)**
```bash
# Playwright or Postman collections
newman run tests/postman/microservices-e2e.json
```

**Timeline:** 3 minggu  
**Owner:** QA Team

---

## 📅 Gantt Chart — 12 Week Plan

```
WEEK 1-2: BLOCKERS
├── Fix perizinan unhealthy status
├── Data sync Phase 1 (critical data)
├── Auth integration testing
└── CRM service scaffold

WEEK 3-4: P1 COMPLETION
├── CRM service implementation
├── Email SMTP testing
├── Finansial Midtrans integration
└── Data sync Phase 2 (transactional)

WEEK 5-6: PERIZINAN DOMAIN
├── Complete AI integration
├── OSS civic_stack integration
├── Document management (MinIO)
└── Testing perizinan flows

WEEK 7-8: STABILIZATION
├── Fix all failing tests
├── Complete test coverage
├── Load testing (100 concurrent)
└── Performance optimization

WEEK 9-10: OBSERVABILITY
├── Sentry setup all services
├── Grafana + Loki dashboards
├── Alert configuration
└── Backup & recovery testing

WEEK 11-12: PRE-PRODUCTION
├── Staging environment setup
├── Production cutover plan
├── Rollback testing
└── Documentation completion

WEEK 13+: PHASED CUTOVER
├── W13: Content/SEO → microservices
├── W14: HRM → microservices
├── W15: Auth → microservices
├── W16: Perizinan → microservices (dual-write 2 weeks)
├── W17: Proyek → microservices
├── W18: Finansial → microservices (dual-write 2 weeks)
```

---

## 🎯 Success Metrics

### Production Readiness Criteria

**Infrastructure:**
- [ ] All 19 containers healthy 72+ hours
- [ ] No container restarts in 7 days
- [ ] Resource usage < 70% (CPU, RAM, Disk)
- [ ] Health checks passing 99.9%

**Functionality:**
- [ ] All 50 tests passing
- [ ] Data sync 100% complete
- [ ] Auth flow tested across all services
- [ ] Critical business flows working (permit application, invoice payment)

**Performance:**
- [ ] API response time p95 < 500ms
- [ ] Gateway overhead < 10ms
- [ ] Queue job processing < 5 min p95
- [ ] Database queries < 100ms p95

**Security:**
- [ ] JWT authentication enforced
- [ ] No public endpoints without auth (except webhooks)
- [ ] Secrets managed via env vars
- [ ] HTTPS enforced (production)
- [ ] Rate limiting configured

**Observability:**
- [ ] Sentry error tracking active
- [ ] Grafana dashboards created
- [ ] Alert rules configured
- [ ] Log retention 30 days
- [ ] Backup tested (RTO < 4 hours)

---

## 🚨 Risk Management

### High-Risk Items

**R1. Data Loss During Migration**
- **Mitigation:** Dual-write period 2-4 weeks per service
- **Validation:** Row count comparison monolith vs microservices
- **Rollback:** Traefik route switching (< 5 min)

**R2. Performance Degradation**
- **Mitigation:** Load testing before cutover
- **Monitoring:** Real-time metrics dashboard
- **Rollback:** Route back to monolith

**R3. Auth Token Compromise**
- **Mitigation:** Short-lived tokens (1 hour), refresh token rotation
- **Detection:** Monitor failed auth attempts
- **Response:** Revoke tokens, force re-login

**R4. Service Communication Timeout**
- **Mitigation:** Circuit breaker pattern, timeout 30s default
- **Fallback:** Graceful degradation
- **Alerting:** Slack notification on 3 consecutive failures

---

## 📞 Stakeholder Communication

### Weekly Status Report Template

```markdown
# Microservices Migration — Week X Update

## Progress This Week
- ✅ [Completed items]
- 🟡 [In progress items]
- ❌ [Blocked items]

## Metrics
- Services: X/8 production-ready
- Tests: X/50 passing
- Data sync: X% complete

## Next Week Plan
- [Priority 1 tasks]
- [Priority 2 tasks]

## Blockers & Risks
- [Blocker description + owner + ETA]

## Help Needed
- [Resource/decision needs]
```

**Distribution:** Product Manager, CTO, Engineering Team  
**Cadence:** Every Friday 3 PM WIB

---

## 📚 Documentation Deliverables

- [ ] API Documentation (Swagger/OpenAPI per service)
- [ ] Architecture Decision Records (ADR)
- [ ] Runbook untuk common operations
- [ ] Incident response playbook
- [ ] Deployment guide
- [ ] Rollback procedures
- [ ] Performance tuning guide
- [ ] Security audit report

---

## ✅ Next Actions (This Week)

### Hari 1-2: Emergency Triage
```bash
# 1. Fix perizinan unhealthy
cd /home/bizmark/bizmark-microservices
docker logs ms_perizinan_service --tail 200 > /tmp/perizinan_debug.log
# Review logs, identify root cause, apply fix

# 2. KBLI schema fix
docker exec ms_postgres psql -U bizmark_ms -d bizmark_perizinan_dev -c "
ALTER TABLE kblis ALTER COLUMN title DROP NOT NULL;
"

# 3. Start data sync script enhancement
nano /home/bizmark/bizmark.id/sync_data_to_microservices.sh
# Add: users, clients, full KBLI, permit_types
```

### Hari 3-4: Data Sync Execution
```bash
# Execute enhanced sync script
cd /home/bizmark/bizmark.id
./sync_data_to_microservices.sh

# Validate sync results
docker exec ms_postgres psql -U bizmark_ms << 'SQL'
\c bizmark_auth_dev
SELECT 'users' as tbl, COUNT(*) FROM users;
\c bizmark_perizinan_dev
SELECT 'kblis' as tbl, COUNT(*) FROM kblis;
SELECT 'clients' as tbl, COUNT(*) FROM clients;
SQL
```

### Hari 5: Auth Integration Testing
```bash
# Run auth test suite
cd /home/bizmark/bizmark-microservices
./scripts/test_auth_integration.sh

# Fix any failures
# Document working flows
```

---

## 📝 Conclusion

**Current State:** 42% complete, infrastructure solid, data & logic gaps significant

**Critical Path:** 
1. Fix perizinan unhealthy (2 days)
2. Complete data sync (2 weeks)
3. CRM service (2 weeks)
4. Complete P1 features (4 weeks)
5. Testing & stabilization (4 weeks)

**Timeline to Production:** 12 weeks aggressive, 16 weeks realistic

**Key Success Factor:** Disciplined execution of weekly milestones + proactive risk mitigation

---

*Dokumen ini adalah living document. Update setiap minggu berdasarkan actual progress.*  
*Last updated: 3 Juni 2026*
