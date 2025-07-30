#!/bin/bash

# Production Monitoring Script
# Comprehensive monitoring for Bizmark UMKM Platform

set -e

# Configuration
PROJECT_NAME="bizmark-umkm"
DOCKER_COMPOSE_FILE="docker-compose.prod.yml"
LOG_DIR="/opt/bizmark/logs"
ALERT_EMAIL="admin@yourdomain.com"
TELEGRAM_BOT_TOKEN=""  # Add your Telegram bot token
TELEGRAM_CHAT_ID=""    # Add your Telegram chat ID

# Thresholds
CPU_THRESHOLD=80
MEMORY_THRESHOLD=85
DISK_THRESHOLD=90
RESPONSE_TIME_THRESHOLD=5000  # milliseconds

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# Create log directory if it doesn't exist
mkdir -p "$LOG_DIR"

# Logging function
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_DIR/monitoring.log"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_DIR/monitoring.log"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1" | tee -a "$LOG_DIR/monitoring.log"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_DIR/monitoring.log"
}

# Send alert notification
send_alert() {
    local subject="$1"
    local message="$2"
    local priority="$3"  # high, medium, low
    
    log "Sending alert: $subject"
    
    # Email notification
    if command -v mail &> /dev/null && [ -n "$ALERT_EMAIL" ]; then
        echo "$message" | mail -s "[$PROJECT_NAME] $subject" "$ALERT_EMAIL"
    fi
    
    # Telegram notification
    if [ -n "$TELEGRAM_BOT_TOKEN" ] && [ -n "$TELEGRAM_CHAT_ID" ]; then
        local telegram_message="🚨 *$PROJECT_NAME Alert*\n\n*$subject*\n\n$message"
        curl -s -X POST "https://api.telegram.org/bot$TELEGRAM_BOT_TOKEN/sendMessage" \
             -d chat_id="$TELEGRAM_CHAT_ID" \
             -d text="$telegram_message" \
             -d parse_mode="Markdown" > /dev/null
    fi
    
    # Log to monitoring log
    echo "[ALERT] $subject: $message" >> "$LOG_DIR/alerts.log"
}

# Check system resources
check_system_resources() {
    log "Checking system resources..."
    
    # CPU usage
    cpu_usage=$(top -bn1 | grep "Cpu(s)" | awk '{print $2}' | awk -F'%' '{print $1}')
    cpu_usage=${cpu_usage%.*}  # Remove decimal part
    
    if [ "$cpu_usage" -gt "$CPU_THRESHOLD" ]; then
        warning "High CPU usage: ${cpu_usage}%"
        send_alert "High CPU Usage" "CPU usage is at ${cpu_usage}%, threshold is ${CPU_THRESHOLD}%" "medium"
    fi
    
    # Memory usage
    memory_info=$(free | grep Mem)
    total_memory=$(echo $memory_info | awk '{print $2}')
    used_memory=$(echo $memory_info | awk '{print $3}')
    memory_usage=$((used_memory * 100 / total_memory))
    
    if [ "$memory_usage" -gt "$MEMORY_THRESHOLD" ]; then
        warning "High memory usage: ${memory_usage}%"
        send_alert "High Memory Usage" "Memory usage is at ${memory_usage}%, threshold is ${MEMORY_THRESHOLD}%" "medium"
    fi
    
    # Disk usage
    disk_usage=$(df /opt/bizmark | awk 'NR==2 {print $5}' | sed 's/%//')
    
    if [ "$disk_usage" -gt "$DISK_THRESHOLD" ]; then
        error "High disk usage: ${disk_usage}%"
        send_alert "High Disk Usage" "Disk usage is at ${disk_usage}%, threshold is ${DISK_THRESHOLD}%" "high"
    fi
    
    # Log current resource usage
    echo "$(date),${cpu_usage},${memory_usage},${disk_usage}" >> "$LOG_DIR/resources.csv"
    
    success "System resources check completed"
}

# Check Docker containers
check_docker_containers() {
    log "Checking Docker containers..."
    
    local containers=("bizmark_frontend" "bizmark_backend" "bizmark_postgres" "bizmark_redis" "bizmark_minio")
    local failed_containers=()
    
    for container in "${containers[@]}"; do
        if docker ps --format '{{.Names}}' | grep -q "^${container}$"; then
            # Container is running, check if it's healthy
            health_status=$(docker inspect --format='{{.State.Health.Status}}' "$container" 2>/dev/null || echo "no-health-check")
            
            if [ "$health_status" = "unhealthy" ]; then
                error "Container $container is unhealthy"
                failed_containers+=("$container (unhealthy)")
            fi
        else
            error "Container $container is not running"
            failed_containers+=("$container (not running)")
        fi
    done
    
    if [ ${#failed_containers[@]} -gt 0 ]; then
        local message="Failed containers: ${failed_containers[*]}"
        send_alert "Container Issues" "$message" "high"
    else
        success "All containers are running properly"
    fi
}

# Check application health
check_application_health() {
    log "Checking application health..."
    
    # Check frontend
    frontend_start_time=$(date +%s%3N)
    if frontend_response=$(curl -s -w "%{http_code}" -o /dev/null --max-time 10 http://localhost:3000/api/health); then
        frontend_end_time=$(date +%s%3N)
        frontend_response_time=$((frontend_end_time - frontend_start_time))
        
        if [ "$frontend_response" = "200" ]; then
            if [ "$frontend_response_time" -gt "$RESPONSE_TIME_THRESHOLD" ]; then
                warning "Frontend slow response: ${frontend_response_time}ms"
            else
                success "Frontend health check passed (${frontend_response_time}ms)"
            fi
        else
            error "Frontend health check failed with status: $frontend_response"
            send_alert "Frontend Down" "Frontend health check failed with HTTP status: $frontend_response" "high"
        fi
    else
        error "Frontend is not responding"
        send_alert "Frontend Unreachable" "Frontend application is not responding" "high"
    fi
    
    # Check backend
    backend_start_time=$(date +%s%3N)
    if backend_response=$(curl -s -w "%{http_code}" -o /dev/null --max-time 10 http://localhost:3001/health); then
        backend_end_time=$(date +%s%3N)
        backend_response_time=$((backend_end_time - backend_start_time))
        
        if [ "$backend_response" = "200" ]; then
            if [ "$backend_response_time" -gt "$RESPONSE_TIME_THRESHOLD" ]; then
                warning "Backend slow response: ${backend_response_time}ms"
            else
                success "Backend health check passed (${backend_response_time}ms)"
            fi
        else
            error "Backend health check failed with status: $backend_response"
            send_alert "Backend Down" "Backend health check failed with HTTP status: $backend_response" "high"
        fi
    else
        error "Backend is not responding"
        send_alert "Backend Unreachable" "Backend application is not responding" "high"
    fi
    
    # Log response times
    echo "$(date),${frontend_response_time:-0},${backend_response_time:-0}" >> "$LOG_DIR/response_times.csv"
}

# Check database health
check_database_health() {
    log "Checking database health..."
    
    if docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T postgres pg_isready -U bizmark_user -d bizmark_db > /dev/null 2>&1; then
        # Get database stats
        db_connections=$(docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T postgres psql -U bizmark_user -d bizmark_db -c "SELECT count(*) FROM pg_stat_activity;" -t | xargs)
        db_size=$(docker-compose -f "$DOCKER_COMPOSE_FILE" exec -T postgres psql -U bizmark_user -d bizmark_db -c "SELECT pg_size_pretty(pg_database_size('bizmark_db'));" -t | xargs)
        
        success "Database is healthy - Connections: $db_connections, Size: $db_size"
        
        # Log database metrics
        echo "$(date),${db_connections},${db_size}" >> "$LOG_DIR/database_metrics.csv"
    else
        error "Database health check failed"
        send_alert "Database Down" "PostgreSQL database is not responding" "high"
    fi
}

# Check SSL certificate
check_ssl_certificate() {
    local domain="yourdomain.com"  # Replace with your actual domain
    
    if command -v openssl &> /dev/null; then
        log "Checking SSL certificate for $domain..."
        
        cert_expiry=$(echo | openssl s_client -servername "$domain" -connect "$domain:443" 2>/dev/null | openssl x509 -noout -dates | grep notAfter | cut -d= -f2)
        
        if [ -n "$cert_expiry" ]; then
            expiry_epoch=$(date -d "$cert_expiry" +%s)
            current_epoch=$(date +%s)
            days_until_expiry=$(((expiry_epoch - current_epoch) / 86400))
            
            if [ "$days_until_expiry" -lt 30 ]; then
                warning "SSL certificate expires in $days_until_expiry days"
                send_alert "SSL Certificate Expiring" "SSL certificate for $domain expires in $days_until_expiry days" "medium"
            else
                success "SSL certificate is valid for $days_until_expiry more days"
            fi
        fi
    fi
}

# Check disk space for critical directories
check_disk_space() {
    log "Checking disk space for critical directories..."
    
    local critical_dirs=("/opt/bizmark/data" "/opt/bizmark/logs" "/opt/bizmark/backups")
    
    for dir in "${critical_dirs[@]}"; do
        if [ -d "$dir" ]; then
            usage=$(df "$dir" | awk 'NR==2 {print $5}' | sed 's/%//')
            if [ "$usage" -gt "$DISK_THRESHOLD" ]; then
                warning "High disk usage in $dir: ${usage}%"
            fi
        fi
    done
}

# Check log file sizes
check_log_sizes() {
    log "Checking log file sizes..."
    
    # Find large log files (>100MB)
    large_logs=$(find "$LOG_DIR" -name "*.log" -size +100M 2>/dev/null)
    
    if [ -n "$large_logs" ]; then
        warning "Large log files found:"
        echo "$large_logs" | while read -r logfile; do
            size=$(du -h "$logfile" | cut -f1)
            warning "  $logfile: $size"
        done
        
        send_alert "Large Log Files" "Some log files are larger than 100MB and may need rotation" "low"
    fi
}

# Generate monitoring report
generate_report() {
    local report_file="$LOG_DIR/monitoring_report_$(date +%Y%m%d_%H%M%S).txt"
    
    {
        echo "=== Bizmark UMKM Monitoring Report ==="
        echo "Generated at: $(date)"
        echo ""
        
        echo "=== System Resources ==="
        echo "CPU Usage: ${cpu_usage:-N/A}%"
        echo "Memory Usage: ${memory_usage:-N/A}%"
        echo "Disk Usage: ${disk_usage:-N/A}%"
        echo ""
        
        echo "=== Container Status ==="
        docker-compose -f "$DOCKER_COMPOSE_FILE" ps
        echo ""
        
        echo "=== Recent Errors ==="
        tail -20 "$LOG_DIR/monitoring.log" | grep ERROR || echo "No recent errors"
        echo ""
        
        echo "=== Database Status ==="
        echo "Connections: ${db_connections:-N/A}"
        echo "Size: ${db_size:-N/A}"
        echo ""
        
        echo "=== Response Times ==="
        echo "Frontend: ${frontend_response_time:-N/A}ms"
        echo "Backend: ${backend_response_time:-N/A}ms"
        
    } > "$report_file"
    
    log "Monitoring report generated: $report_file"
}

# Main monitoring function
main() {
    log "Starting monitoring check for $PROJECT_NAME..."
    
    # Initialize CSV headers if files don't exist
    [ ! -f "$LOG_DIR/resources.csv" ] && echo "timestamp,cpu_usage,memory_usage,disk_usage" > "$LOG_DIR/resources.csv"
    [ ! -f "$LOG_DIR/response_times.csv" ] && echo "timestamp,frontend_ms,backend_ms" > "$LOG_DIR/response_times.csv"
    [ ! -f "$LOG_DIR/database_metrics.csv" ] && echo "timestamp,connections,size" > "$LOG_DIR/database_metrics.csv"
    
    # Change to project directory
    cd /opt/bizmark || exit 1
    
    # Run all checks
    check_system_resources
    check_docker_containers
    check_application_health
    check_database_health
    check_ssl_certificate
    check_disk_space
    check_log_sizes
    
    # Generate report
    generate_report
    
    success "Monitoring check completed"
}

# Handle different script arguments
case "${1:-}" in
    "resources")
        check_system_resources
        ;;
    "containers")
        check_docker_containers
        ;;
    "health")
        check_application_health
        ;;
    "database")
        check_database_health
        ;;
    "ssl")
        check_ssl_certificate
        ;;
    "report")
        generate_report
        ;;
    *)
        main
        ;;
esac
