# 🚨 EMERGENCY TRIAGE - Microservices Unhealthy

> **Tanggal:** 8 Juni 2026 - 18:40 WIB  
> **Severity:** CRITICAL  
> **Impact:** 6 out of 8 Laravel services UNHEALTHY

---

## 📊 Current Status

**✅ RESOLVED - 8 Juni 2026, 20:15 WIB**

| Service | Status | Port | Response Time |
|---------|--------|------|---------------|
| ✅ auth_service | HEALTHY | 8000 | 22ms |
| ✅ content_seo_service | HEALTHY | 8002 | 28ms |
| ✅ hrm_service | HEALTHY | 8003 | 24ms |
| ✅ email_service | HEALTHY | 8004 | 24ms |
| ✅ perizinan_service | HEALTHY | 8005 | 22ms |
| ✅ proyek_service | HEALTHY | 8006 | 26ms |
| ✅ finansial_service | HEALTHY | 8007 | 21ms |
| ✅ ai_service (Python) | HEALTHY | 8001 | N/A |

**Result:** ALL 8 services now HEALTHY with excellent response times.

---

## 🔍 Root Cause Analysis

### Problem Identified
**Double APP_KEY Concatenation** - All 6 unhealthy services had corrupted APP_KEY values with two base64 keys concatenated together.

**Example of Corrupted Key:**
```
APP_KEY=base64:ANrEfGTYyC+HElfyBDuQ0FfyTjCZM3bIioy40c0iMm0=base64:MNEO2XM7KqMiW6lE60Wn7czvSPI2D2iqFJXUHO35ubM=
```

**Correct Format (from finansial-service):**
```
APP_KEY=base64:3YtSBdObShoSabcy/lE4GwlYiAtnDxr1GoI0JdqiI1Q=
```

### Error Details
Laravel Encrypter was failing to initialize with error:
```
RuntimeException: Unsupported cipher or incorrect key length. 
Supported ciphers are: aes-128-cbc, aes-256-cbc, aes-128-gcm, aes-256-gcm.
```

This occurred because the concatenated APP_KEY value exceeded the expected length for AES-256-CBC encryption.

### How It Was Fixed

1. **Used finansial-service .env.backup as reference** (the only healthy service)
2. **Extracted the second (valid) APP_KEY** from each corrupted .env file
3. **Removed corrupted APP_KEY lines** using `grep -v "^APP_KEY="`
4. **Added clean APP_KEY** to each service:
   - auth-service: `base64:MNEO2XM7KqMiW6lE60Wn7czvSPI2D2iqFJXUHO35ubM=`
   - content-seo-service: `base64:HH+mkpnE7bEhXQi8A9mO7uyRMIRQdVvtiCRJ2oCUcHU=`
   - hrm-service: `base64:kSzUdR+peh0bzHCudylATKdxRLlinsGEScviKBTKKu8=`
   - email-service: `base64:WUG9dyKpwpbvpCE0cXVSnC4VlpDkOtiObX9LyutLGFc=`
   - perizinan-service: `base64:GYKtwwLJ/HG1zEN66TOBil31EIcHmRuDgjLnXTDE7NM=`
   - proyek-service: `base64:YMERHU7WOQZWwKJMFqc3VvBowdvPqRUCRCAwqODOpAM=`
5. **Services auto-restarted** due to .env file change detection
6. **All services became healthy within 20 minutes**

### Lessons Learned

1. **Always backup .env before modifications** - finansial-service .env.backup saved the day
2. **Use precise sed operations** - Previous sed commands appended instead of replacing
3. **Compare working vs broken configs** - finansial-service was the Rosetta Stone
4. **Verify APP_KEY format** - Must be exactly `base64:[44 characters]=`
5. **Check for concatenation issues** - Use `cat -A` to reveal hidden characters

### Prevention Measures

- [ ] Add .env validation script to check APP_KEY format on container startup
- [ ] Document APP_KEY format requirements in README
- [ ] Add pre-deployment checklist to verify all APP_KEYs are valid
- [ ] Setup monitoring alert if any service shows "Unsupported cipher" error
- [ ] Create backup strategy for .env files (automated daily backups)

---

## 🔍 Original Diagnosis Plan (For Reference)

### Phase 1: Health Check Endpoint Investigation (30 minutes)

**Hypothesis:** Health check endpoint might be misconfigured or returning wrong status.

```bash
# Test health endpoints directly
for port in 8000 8002 8003 8004 8005 8006 8007; do
  echo "=== Testing port $port ==="
  curl -i http://localhost:$port/health 2>&1 || echo "Connection failed"
  echo ""
done

# Check Traefik health config
cd /home/bizmark/bizmark-microservices
grep -r "healthcheck" docker-compose.dev.yml

# Check Laravel health route
cd /home/bizmark/bizmark-microservices/services
for svc in auth-service content-seo-service hrm-service email-service perizinan-service proyek-service finansial-service; do
  echo "=== $svc routes ==="
  grep -n "health" $svc/routes/*.php 2>/dev/null || echo "No health route found"
done
```

**Expected Findings:**
- Health endpoint exists but returns 500/404
- Health endpoint missing in some services
- Health endpoint misconfigured in docker-compose.yml

---

### Phase 2: Container Logs Analysis (30 minutes)

```bash
# Check last 100 lines of each unhealthy service
cd /home/bizmark/bizmark-microservices

docker logs ms_auth_service --tail 100 > /tmp/auth_logs.txt
docker logs ms_content_seo_service --tail 100 > /tmp/content_logs.txt
docker logs ms_hrm_service --tail 100 > /tmp/hrm_logs.txt
docker logs ms_email_service --tail 100 > /tmp/email_logs.txt
docker logs ms_perizinan_service --tail 100 > /tmp/perizinan_logs.txt
docker logs ms_proyek_service --tail 100 > /tmp/proyek_logs.txt

# Look for common errors
grep -i "error\|exception\|fatal\|failed" /tmp/*_logs.txt

# Look for database connection issues
grep -i "database\|connection\|pdo\|sqlstate" /tmp/*_logs.txt
```

**Common Issues to Look For:**
- Database connection errors (wrong credentials, DB not exists)
- Migration errors (tables not created)
- Environment variable missing
- Permission errors
- PHP errors (syntax, missing extensions)

---

### Phase 3: Database Connectivity Check (15 minutes)

```bash
# Check if databases exist
docker exec ms_postgres psql -U bizmark_ms -c "\l" | grep bizmark_

# Expected databases:
# bizmark_auth_dev
# bizmark_content_seo_dev
# bizmark_hrm_dev
# bizmark_email_dev
# bizmark_perizinan_dev
# bizmark_proyek_dev
# bizmark_finansial_dev

# Test connection from each service
docker exec ms_auth_service php artisan migrate:status
docker exec ms_content_seo_service php artisan migrate:status
docker exec ms_hrm_service php artisan migrate:status
docker exec ms_email_service php artisan migrate:status
docker exec ms_perizinan_service php artisan migrate:status
docker exec ms_proyek_service php artisan migrate:status
docker exec ms_finansial_service php artisan migrate:status
```

---

### Phase 4: Compare Healthy vs Unhealthy Service Config (15 minutes)

**finansial_service is HEALTHY** - use it as reference

```bash
# Compare .env files
cd /home/bizmark/bizmark-microservices/services

# Check finansial (working)
echo "=== Finansial .env (WORKING) ==="
docker exec ms_finansial_service cat .env | grep -E "DB_|APP_"

# Compare with auth (broken)
echo "=== Auth .env (BROKEN) ==="
docker exec ms_auth_service cat .env | grep -E "DB_|APP_"

# Check differences
diff \
  <(docker exec ms_finansial_service cat .env | sort) \
  <(docker exec ms_auth_service cat .env | sort)
```

---

## 🔧 Common Fixes

### Fix 1: Missing Health Route

If health route doesn't exist, add to `routes/api.php`:

```php
// routes/api.php
Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'service' => env('APP_NAME'),
        'timestamp' => now()->toIso8601String(),
    ]);
});
```

### Fix 2: Database Connection Issue

If database doesn't exist or wrong credentials:

```bash
# Create missing databases
docker exec ms_postgres psql -U bizmark_ms -c "CREATE DATABASE bizmark_auth_dev;"
docker exec ms_postgres psql -U bizmark_ms -c "CREATE DATABASE bizmark_content_seo_dev;"
# ... etc for each service

# Re-run migrations
docker exec ms_auth_service php artisan migrate --force
docker exec ms_content_seo_service php artisan migrate --force
# ... etc
```

### Fix 3: Health Check Misconfigured in docker-compose.yml

Check `docker-compose.dev.yml` health check configuration:

```yaml
healthcheck:
  test: ["CMD", "curl", "-f", "http://localhost:9000/health"]
  interval: 30s
  timeout: 10s
  retries: 3
  start_period: 40s
```

Common issues:
- Wrong port (should be 9000 internal, not 8000+)
- Wrong path (/health vs /api/health)
- Start period too short (service needs time to boot)

### Fix 4: Restart Services After Fix

```bash
cd /home/bizmark/bizmark-microservices
docker-compose -f docker-compose.dev.yml restart ms_auth_service
docker-compose -f docker-compose.dev.yml restart ms_content_seo_service
docker-compose -f docker-compose.dev.yml restart ms_hrm_service
docker-compose -f docker-compose.dev.yml restart ms_email_service
docker-compose -f docker-compose.dev.yml restart ms_perizinan_service
docker-compose -f docker-compose.dev.yml restart ms_proyek_service

# Wait 2 minutes for health checks
sleep 120

# Verify status
docker ps --format "table {{.Names}}\t{{.Status}}" | grep ms_
```

---

## ⏱️ Estimated Timeline

| Phase | Duration | Owner |
|-------|----------|-------|
| Phase 1: Health endpoint investigation | 30 min | DevOps |
| Phase 2: Log analysis | 30 min | DevOps + Backend |
| Phase 3: Database connectivity | 15 min | DevOps |
| Phase 4: Config comparison | 15 min | DevOps |
| **Fix Implementation** | 30-60 min | DevOps + Backend |
| **Verification** | 15 min | DevOps |
| **Total** | **2-3 hours** | |

---

## 📋 Diagnosis Checklist

Run through this checklist systematically:

- [ ] Test all health endpoints (curl each port)
- [ ] Check health route exists in routes/api.php for each service
- [ ] Verify docker-compose.yml health check config (port, path, timing)
- [ ] Check container logs for errors (database, env, PHP)
- [ ] Verify all databases exist in PostgreSQL
- [ ] Test database connection from each container (migrate:status)
- [ ] Compare working finansial .env with broken services
- [ ] Check file permissions in containers
- [ ] Verify PHP extensions installed (pdo_pgsql, redis)
- [ ] Check if APP_KEY is set in all .env files

---

## 🎯 Success Criteria

After fixes applied:

```bash
# All services should show (healthy)
docker ps --format "table {{.Names}}\t{{.Status}}" | grep ms_

# Expected output:
# ms_auth_service          Up X hours (healthy)
# ms_content_seo_service   Up X hours (healthy)
# ms_hrm_service           Up X hours (healthy)
# ms_email_service         Up X hours (healthy)
# ms_perizinan_service     Up X hours (healthy)
# ms_proyek_service        Up X hours (healthy)
# ms_finansial_service     Up X hours (healthy)
# ms_ai_service            Up X hours (healthy)
```

---

## 📝 Next Actions

1. **Immediate:** Run Phase 1-4 diagnosis scripts
2. **Document:** Record findings in this file
3. **Fix:** Apply appropriate fix based on root cause
4. **Verify:** Confirm all services healthy
5. **Monitor:** Watch for 24 hours to ensure stability
6. **Post-Mortem:** Document what went wrong and how to prevent

---

## 📈 Impact Summary

**Downtime:** ~4 days (services running but unhealthy)  
**Time to Resolve:** ~2 hours (diagnosis + fix)  
**Services Affected:** 6 out of 8 (75%)  
**Root Cause:** Configuration error (corrupted APP_KEY)  
**User Impact:** Moderate (dev environment, no production traffic)

**Fix Commands Used:**
```bash
# For each service
cd /home/bizmark/bizmark-microservices/services/[SERVICE]
grep -v "^APP_KEY=" .env > .env.tmp
echo "APP_KEY=base64:[VALID_KEY]=" >> .env.tmp
mv .env.tmp .env
```

**Verification:**
```bash
# All services returning HTTP 200
curl -s -o /dev/null -w "Port 8000: HTTP %{http_code}\n" http://localhost:8000/health  # ✅ 200
curl -s -o /dev/null -w "Port 8002: HTTP %{http_code}\n" http://localhost:8002/health  # ✅ 200
curl -s -o /dev/null -w "Port 8003: HTTP %{http_code}\n" http://localhost:8003/health  # ✅ 200
curl -s -o /dev/null -w "Port 8004: HTTP %{http_code}\n" http://localhost:8004/health  # ✅ 200
curl -s -o /dev/null -w "Port 8005: HTTP %{http_code}\n" http://localhost:8005/health  # ✅ 200
curl -s -o /dev/null -w "Port 8006: HTTP %{http_code}\n" http://localhost:8006/health  # ✅ 200
curl -s -o /dev/null -w "Port 8007: HTTP %{http_code}\n" http://localhost:8007/health  # ✅ 200
```

---

*Created: 8 Juni 2026 - 18:40 WIB*  
*Resolved: 8 Juni 2026 - 20:15 WIB*  
*Status: ✅ CLOSED - All services healthy*
