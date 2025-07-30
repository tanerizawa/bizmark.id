'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/contexts/AuthContext';

interface AuditLog {
  id: string;
  timestamp: string;
  userId: string;
  userName: string;
  userRole: string;
  action: string;
  module: string;
  resourceId?: string;
  resourceType?: string;
  changes?: Record<string, { old: string | number | boolean | null; new: string | number | boolean | null }>;
  ipAddress: string;
  userAgent: string;
  outcome: 'success' | 'failure' | 'warning';
  riskLevel: 'low' | 'medium' | 'high' | 'critical';
  compliance?: string[];
  details: string;
}

interface ComplianceMetrics {
  totalAudits: number;
  criticalIssues: number;
  resolvedIssues: number;
  pendingIssues: number;
  complianceScore: number;
  lastAuditDate: string;
  nextAuditDate: string;
  regulations: Array<{
    name: string;
    status: 'compliant' | 'non-compliant' | 'pending';
    lastCheck: string;
    issues: number;
  }>;
}

interface SecurityAlert {
  id: string;
  timestamp: string;
  type: 'authentication' | 'authorization' | 'data_access' | 'system' | 'compliance';
  severity: 'low' | 'medium' | 'high' | 'critical';
  title: string;
  description: string;
  user?: string;
  source: string;
  status: 'open' | 'investigating' | 'resolved' | 'false_positive';
  assignedTo?: string;
}

export default function AuditCompliancePage() {
  const { user } = useAuth();
  const [activeTab, setActiveTab] = useState('audit-logs');
  const [auditLogs, setAuditLogs] = useState<AuditLog[]>([]);
  const [complianceMetrics, setComplianceMetrics] = useState<ComplianceMetrics | null>(null);
  const [securityAlerts, setSecurityAlerts] = useState<SecurityAlert[]>([]);
  const [loading, setLoading] = useState(true);
  const [filters, setFilters] = useState({
    dateFrom: '',
    dateTo: '',
    userId: '',
    action: '',
    module: '',
    riskLevel: '',
    outcome: ''
  });

  useEffect(() => {
    const fetchAuditData = async () => {
      try {
        setLoading(true);
        
        // Simulate API calls
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        setComplianceMetrics({
          totalAudits: 1247,
          criticalIssues: 3,
          resolvedIssues: 456,
          pendingIssues: 12,
          complianceScore: 94.2,
          lastAuditDate: '2025-07-25',
          nextAuditDate: '2025-08-25',
          regulations: [
            { name: 'GDPR', status: 'compliant', lastCheck: '2025-07-25', issues: 0 },
            { name: 'PDP Indonesia', status: 'compliant', lastCheck: '2025-07-24', issues: 0 },
            { name: 'PP 71/2019', status: 'pending', lastCheck: '2025-07-20', issues: 2 },
            { name: 'ISO 27001', status: 'compliant', lastCheck: '2025-07-23', issues: 0 },
            { name: 'SOX', status: 'non-compliant', lastCheck: '2025-07-22', issues: 1 }
          ]
        });

        // Sample audit logs
        setAuditLogs([
          {
            id: '1',
            timestamp: '2025-07-30T10:30:00Z',
            userId: 'usr_001',
            userName: 'John Doe',
            userRole: 'admin',
            action: 'UPDATE_APPLICATION_STATUS',
            module: 'applications',
            resourceId: 'app_123',
            resourceType: 'application',
            changes: {
              status: { old: 'pending', new: 'approved' },
              approvedBy: { old: null, new: 'usr_001' }
            },
            ipAddress: '192.168.1.100',
            userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            outcome: 'success',
            riskLevel: 'medium',
            compliance: ['PDP Indonesia', 'PP 71/2019'],
            details: 'Application status updated from pending to approved'
          },
          {
            id: '2',
            timestamp: '2025-07-30T10:25:00Z',
            userId: 'usr_002',
            userName: 'Jane Smith',
            userRole: 'operator',
            action: 'LOGIN_FAILED',
            module: 'authentication',
            ipAddress: '192.168.1.105',
            userAgent: 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
            outcome: 'failure',
            riskLevel: 'high',
            details: 'Failed login attempt - invalid password'
          },
          {
            id: '3',
            timestamp: '2025-07-30T10:20:00Z',
            userId: 'usr_003',
            userName: 'Admin User',
            userRole: 'super_admin',
            action: 'EXPORT_SENSITIVE_DATA',
            module: 'reports',
            resourceType: 'user_data',
            ipAddress: '192.168.1.102',
            userAgent: 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
            outcome: 'success',
            riskLevel: 'critical',
            compliance: ['GDPR', 'PDP Indonesia'],
            details: 'Exported user personal data report'
          },
          {
            id: '4',
            timestamp: '2025-07-30T10:15:00Z',
            userId: 'usr_004',
            userName: 'System User',
            userRole: 'system',
            action: 'AUTOMATED_BACKUP',
            module: 'system',
            ipAddress: '127.0.0.1',
            userAgent: 'System/1.0',
            outcome: 'success',
            riskLevel: 'low',
            details: 'Automated database backup completed'
          }
        ]);

        // Sample security alerts
        setSecurityAlerts([
          {
            id: 'alert_001',
            timestamp: '2025-07-30T10:35:00Z',
            type: 'authentication',
            severity: 'high',
            title: 'Multiple Failed Login Attempts',
            description: 'User usr_002 has exceeded the maximum number of failed login attempts',
            user: 'Jane Smith',
            source: 'Authentication Service',
            status: 'open',
            assignedTo: 'Security Team'
          },
          {
            id: 'alert_002',
            timestamp: '2025-07-30T09:45:00Z',
            type: 'data_access',
            severity: 'critical',
            title: 'Sensitive Data Export',
            description: 'Large volume of sensitive user data exported outside business hours',
            user: 'Admin User',
            source: 'Data Loss Prevention',
            status: 'investigating',
            assignedTo: 'John Doe'
          },
          {
            id: 'alert_003',
            timestamp: '2025-07-30T08:30:00Z',
            type: 'system',
            severity: 'medium',
            title: 'Unusual System Activity',
            description: 'Abnormal database query patterns detected',
            source: 'System Monitor',
            status: 'resolved'
          }
        ]);

      } catch (error) {
        console.error('Error fetching audit data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchAuditData();
  }, [filters]);

  const handleFilterChange = (key: string, value: string) => {
    setFilters(prev => ({
      ...prev,
      [key]: value
    }));
  };

  const handleAlertAction = (alertId: string, action: 'resolve' | 'investigate' | 'false_positive') => {
    setSecurityAlerts(prev => prev.map(alert => 
      alert.id === alertId 
        ? { ...alert, status: action === 'resolve' ? 'resolved' : action === 'investigate' ? 'investigating' : 'false_positive' }
        : alert
    ));
  };

  const exportAuditLog = (format: 'csv' | 'pdf' | 'json') => {
    const exportData = {
      logs: auditLogs,
      filters,
      exportedAt: new Date().toISOString(),
      exportedBy: user?.profile.fullName
    };
    
    const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `audit-log-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  };

  const getRiskLevelColor = (level: string) => {
    switch (level) {
      case 'low': return 'text-green-600 bg-green-100';
      case 'medium': return 'text-yellow-600 bg-yellow-100';
      case 'high': return 'text-orange-600 bg-orange-100';
      case 'critical': return 'text-red-600 bg-red-100';
      default: return 'text-gray-600 bg-gray-100';
    }
  };

  const getSeverityColor = (severity: string) => {
    switch (severity) {
      case 'low': return 'text-green-600 bg-green-100';
      case 'medium': return 'text-yellow-600 bg-yellow-100';
      case 'high': return 'text-orange-600 bg-orange-100';
      case 'critical': return 'text-red-600 bg-red-100';
      default: return 'text-gray-600 bg-gray-100';
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'success': return 'text-green-600 bg-green-100';
      case 'failure': return 'text-red-600 bg-red-100';
      case 'warning': return 'text-yellow-600 bg-yellow-100';
      case 'open': return 'text-red-600 bg-red-100';
      case 'investigating': return 'text-yellow-600 bg-yellow-100';
      case 'resolved': return 'text-green-600 bg-green-100';
      case 'false_positive': return 'text-gray-600 bg-gray-100';
      default: return 'text-gray-600 bg-gray-100';
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">Loading audit and compliance data...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Header */}
        <div className="mb-8">
          <div className="flex justify-between items-center">
            <div>
              <h1 className="text-3xl font-bold text-gray-900">Audit & Compliance</h1>
              <p className="mt-2 text-gray-600">Comprehensive audit logging and compliance monitoring</p>
            </div>
            <div className="flex space-x-3">
              <button
                onClick={() => exportAuditLog('json')}
                className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
              >
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Logs
              </button>
            </div>
          </div>
        </div>

        {/* Compliance Overview Cards */}
        {complianceMetrics && (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div className="bg-white rounded-lg shadow-sm p-6">
              <div className="flex items-center">
                <div className="p-2 bg-blue-100 rounded-lg">
                  <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div className="ml-4">
                  <h3 className="text-sm font-medium text-gray-500">Compliance Score</h3>
                  <p className="text-2xl font-bold text-gray-900">{complianceMetrics.complianceScore}%</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-sm p-6">
              <div className="flex items-center">
                <div className="p-2 bg-red-100 rounded-lg">
                  <svg className="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                  </svg>
                </div>
                <div className="ml-4">
                  <h3 className="text-sm font-medium text-gray-500">Critical Issues</h3>
                  <p className="text-2xl font-bold text-gray-900">{complianceMetrics.criticalIssues}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-sm p-6">
              <div className="flex items-center">
                <div className="p-2 bg-yellow-100 rounded-lg">
                  <svg className="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
                <div className="ml-4">
                  <h3 className="text-sm font-medium text-gray-500">Pending Issues</h3>
                  <p className="text-2xl font-bold text-gray-900">{complianceMetrics.pendingIssues}</p>
                </div>
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-sm p-6">
              <div className="flex items-center">
                <div className="p-2 bg-green-100 rounded-lg">
                  <svg className="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                  </svg>
                </div>
                <div className="ml-4">
                  <h3 className="text-sm font-medium text-gray-500">Resolved Issues</h3>
                  <p className="text-2xl font-bold text-gray-900">{complianceMetrics.resolvedIssues}</p>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Tabs */}
        <div className="bg-white rounded-lg shadow-sm mb-8">
          <div className="border-b border-gray-200">
            <nav className="-mb-px flex space-x-8 px-6">
              {[
                { id: 'audit-logs', name: 'Audit Logs' },
                { id: 'security-alerts', name: 'Security Alerts' },
                { id: 'compliance', name: 'Compliance Status' },
                { id: 'reports', name: 'Audit Reports' }
              ].map((tab) => (
                <button
                  key={tab.id}
                  onClick={() => setActiveTab(tab.id)}
                  className={`py-4 px-1 border-b-2 font-medium text-sm ${
                    activeTab === tab.id
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  {tab.name}
                </button>
              ))}
            </nav>
          </div>

          <div className="p-6">
            {activeTab === 'audit-logs' && (
              <div className="space-y-6">
                {/* Filters */}
                <div className="bg-gray-50 rounded-lg p-4">
                  <h3 className="text-lg font-semibold mb-4">Filter Audit Logs</h3>
                  <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                      <input
                        type="date"
                        value={filters.dateFrom}
                        onChange={(e) => handleFilterChange('dateFrom', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                      <input
                        type="date"
                        value={filters.dateTo}
                        onChange={(e) => handleFilterChange('dateTo', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">User</label>
                      <input
                        type="text"
                        placeholder="User ID or Name"
                        value={filters.userId}
                        onChange={(e) => handleFilterChange('userId', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      />
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Action</label>
                      <select
                        value={filters.action}
                        onChange={(e) => handleFilterChange('action', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="">All Actions</option>
                        <option value="LOGIN">Login</option>
                        <option value="LOGOUT">Logout</option>
                        <option value="CREATE">Create</option>
                        <option value="UPDATE">Update</option>
                        <option value="DELETE">Delete</option>
                        <option value="EXPORT">Export</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Risk Level</label>
                      <select
                        value={filters.riskLevel}
                        onChange={(e) => handleFilterChange('riskLevel', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="">All Levels</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                      </select>
                    </div>
                    <div>
                      <label className="block text-sm font-medium text-gray-700 mb-1">Outcome</label>
                      <select
                        value={filters.outcome}
                        onChange={(e) => handleFilterChange('outcome', e.target.value)}
                        className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                      >
                        <option value="">All Outcomes</option>
                        <option value="success">Success</option>
                        <option value="failure">Failure</option>
                        <option value="warning">Warning</option>
                      </select>
                    </div>
                  </div>
                </div>

                {/* Audit Logs Table */}
                <div className="bg-white border border-gray-200 rounded-lg overflow-hidden">
                  <div className="overflow-x-auto">
                    <table className="w-full">
                      <thead className="bg-gray-50">
                        <tr>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Risk Level</th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Outcome</th>
                          <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                        </tr>
                      </thead>
                      <tbody className="bg-white divide-y divide-gray-200">
                        {auditLogs.map((log) => (
                          <tr key={log.id} className="hover:bg-gray-50">
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              {new Date(log.timestamp).toLocaleString()}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <div className="text-sm font-medium text-gray-900">{log.userName}</div>
                              <div className="text-sm text-gray-500">{log.userRole}</div>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              {log.action}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                              {log.module}
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getRiskLevelColor(log.riskLevel)}`}>
                                {log.riskLevel}
                              </span>
                            </td>
                            <td className="px-6 py-4 whitespace-nowrap">
                              <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(log.outcome)}`}>
                                {log.outcome}
                              </span>
                            </td>
                            <td className="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                              {log.details}
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'security-alerts' && (
              <div className="space-y-6">
                <div className="grid grid-cols-1 gap-6">
                  {securityAlerts.map((alert) => (
                    <div key={alert.id} className="bg-white border border-gray-200 rounded-lg p-6">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <div className="flex items-center space-x-3 mb-2">
                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getSeverityColor(alert.severity)}`}>
                              {alert.severity}
                            </span>
                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(alert.status)}`}>
                              {alert.status}
                            </span>
                            <span className="text-sm text-gray-500">
                              {new Date(alert.timestamp).toLocaleString()}
                            </span>
                          </div>
                          <h3 className="text-lg font-semibold text-gray-900 mb-2">{alert.title}</h3>
                          <p className="text-gray-600 mb-3">{alert.description}</p>
                          <div className="flex items-center space-x-4 text-sm text-gray-500">
                            <span>Source: {alert.source}</span>
                            {alert.user && <span>User: {alert.user}</span>}
                            {alert.assignedTo && <span>Assigned to: {alert.assignedTo}</span>}
                          </div>
                        </div>
                        <div className="flex space-x-2">
                          {alert.status === 'open' && (
                            <>
                              <button
                                onClick={() => handleAlertAction(alert.id, 'investigate')}
                                className="bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700 transition-colors"
                              >
                                Investigate
                              </button>
                              <button
                                onClick={() => handleAlertAction(alert.id, 'resolve')}
                                className="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors"
                              >
                                Resolve
                              </button>
                              <button
                                onClick={() => handleAlertAction(alert.id, 'false_positive')}
                                className="bg-gray-600 text-white px-3 py-1 rounded text-sm hover:bg-gray-700 transition-colors"
                              >
                                False Positive
                              </button>
                            </>
                          )}
                          {alert.status === 'investigating' && (
                            <button
                              onClick={() => handleAlertAction(alert.id, 'resolve')}
                              className="bg-green-600 text-white px-3 py-1 rounded text-sm hover:bg-green-700 transition-colors"
                            >
                              Mark Resolved
                            </button>
                          )}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {activeTab === 'compliance' && complianceMetrics && (
              <div className="space-y-6">
                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-semibold mb-4">Regulatory Compliance Status</h3>
                  <div className="space-y-4">
                    {complianceMetrics.regulations.map((regulation, index) => (
                      <div key={index} className="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                          <h4 className="font-medium text-gray-900">{regulation.name}</h4>
                          <p className="text-sm text-gray-500">Last checked: {new Date(regulation.lastCheck).toLocaleDateString()}</p>
                        </div>
                        <div className="flex items-center space-x-4">
                          <span className="text-sm text-gray-600">
                            {regulation.issues} issue{regulation.issues !== 1 ? 's' : ''}
                          </span>
                          <span className={`inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${
                            regulation.status === 'compliant' ? 'text-green-700 bg-green-100' :
                            regulation.status === 'non-compliant' ? 'text-red-700 bg-red-100' :
                            'text-yellow-700 bg-yellow-100'
                          }`}>
                            {regulation.status}
                          </span>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
}
