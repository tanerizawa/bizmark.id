'use client';

import { useState, useEffect } from 'react';
import { useAuth } from '@/contexts/AuthContext';
import { useNotifications } from '@/contexts/NotificationContext';

interface APIEndpoint {
  id: string;
  method: 'GET' | 'POST' | 'PUT' | 'DELETE' | 'PATCH';
  path: string;
  description: string;
  status: 'active' | 'deprecated' | 'maintenance';
  version: string;
  requestCount: number;
  avgResponseTime: number;
  errorRate: number;
  lastAccessed: Date;
  rateLimitEnabled: boolean;
  requiresAuth: boolean;
  tags: string[];
}

interface APIKey {
  id: string;
  name: string;
  key: string;
  status: 'active' | 'revoked' | 'expired';
  permissions: string[];
  requestCount: number;
  lastUsed: Date;
  expiresAt: Date;
  createdAt: Date;
}

export default function APIManagementPage() {
  const { user } = useAuth();
  const { addNotification } = useNotifications();
  const [activeTab, setActiveTab] = useState('endpoints');
  const [loading, setLoading] = useState(true);
  const [searchTerm, setSearchTerm] = useState('');
  
  const [endpoints, setEndpoints] = useState<APIEndpoint[]>([
    {
      id: '1',
      method: 'GET',
      path: '/api/auth/profile',
      description: 'Get user profile information',
      status: 'active',
      version: 'v1',
      requestCount: 15420,
      avgResponseTime: 145,
      errorRate: 0.2,
      lastAccessed: new Date(Date.now() - 2 * 60 * 60 * 1000),
      rateLimitEnabled: true,
      requiresAuth: true,
      tags: ['auth', 'user']
    },
    {
      id: '2',
      method: 'POST',
      path: '/api/businesses',
      description: 'Create new business',
      status: 'active',
      version: 'v1',
      requestCount: 8932,
      avgResponseTime: 320,
      errorRate: 1.5,
      lastAccessed: new Date(Date.now() - 30 * 60 * 1000),
      rateLimitEnabled: true,
      requiresAuth: true,
      tags: ['business', 'create']
    },
    {
      id: '3',
      method: 'GET',
      path: '/api/licenses/types',
      description: 'Get available license types',
      status: 'active',
      version: 'v1',
      requestCount: 25680,
      avgResponseTime: 85,
      errorRate: 0.1,
      lastAccessed: new Date(Date.now() - 5 * 60 * 1000),
      rateLimitEnabled: false,
      requiresAuth: false,
      tags: ['license', 'public']
    },
    {
      id: '4',
      method: 'POST',
      path: '/api/documents/upload',
      description: 'Upload document files',
      status: 'active',
      version: 'v1',
      requestCount: 12450,
      avgResponseTime: 1250,
      errorRate: 3.2,
      lastAccessed: new Date(Date.now() - 15 * 60 * 1000),
      rateLimitEnabled: true,
      requiresAuth: true,
      tags: ['document', 'upload']
    },
    {
      id: '5',
      method: 'GET',
      path: '/api/analytics/dashboard',
      description: 'Get dashboard analytics',
      status: 'deprecated',
      version: 'v1',
      requestCount: 3421,
      avgResponseTime: 420,
      errorRate: 0.8,
      lastAccessed: new Date(Date.now() - 24 * 60 * 60 * 1000),
      rateLimitEnabled: true,
      requiresAuth: true,
      tags: ['analytics', 'deprecated']
    }
  ]);

  const [apiKeys, setApiKeys] = useState<APIKey[]>([
    {
      id: '1',
      name: 'Production Frontend',
      key: 'bz_prod_key_abc123...',
      status: 'active',
      permissions: ['read:businesses', 'write:applications', 'read:licenses'],
      requestCount: 45680,
      lastUsed: new Date(Date.now() - 30 * 60 * 1000),
      expiresAt: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000),
      createdAt: new Date(Date.now() - 90 * 24 * 60 * 60 * 1000)
    },
    {
      id: '2',
      name: 'Mobile App',
      key: 'bz_mobile_key_def456...',
      status: 'active',
      permissions: ['read:businesses', 'read:applications', 'read:licenses'],
      requestCount: 28934,
      lastUsed: new Date(Date.now() - 2 * 60 * 60 * 1000),
      expiresAt: new Date(Date.now() + 180 * 24 * 60 * 60 * 1000),
      createdAt: new Date(Date.now() - 45 * 24 * 60 * 60 * 1000)
    },
    {
      id: '3',
      name: 'Development Environment',
      key: 'bz_dev_key_ghi789...',
      status: 'active',
      permissions: ['read:*', 'write:*'],
      requestCount: 15420,
      lastUsed: new Date(Date.now() - 5 * 60 * 1000),
      expiresAt: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000),
      createdAt: new Date(Date.now() - 15 * 24 * 60 * 60 * 1000)
    },
    {
      id: '4',
      name: 'Legacy Integration',
      key: 'bz_legacy_key_jkl012...',
      status: 'revoked',
      permissions: ['read:businesses'],
      requestCount: 8523,
      lastUsed: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000),
      expiresAt: new Date(Date.now() - 10 * 24 * 60 * 60 * 1000),
      createdAt: new Date(Date.now() - 120 * 24 * 60 * 60 * 1000)
    }
  ]);

  useEffect(() => {
    setLoading(false);
  }, []);

  const filteredEndpoints = endpoints.filter(endpoint =>
    endpoint.path.toLowerCase().includes(searchTerm.toLowerCase()) ||
    endpoint.description.toLowerCase().includes(searchTerm.toLowerCase()) ||
    endpoint.tags.some(tag => tag.toLowerCase().includes(searchTerm.toLowerCase()))
  );

  const filteredApiKeys = apiKeys.filter(key =>
    key.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
    key.permissions.some(perm => perm.toLowerCase().includes(searchTerm.toLowerCase()))
  );

  const toggleEndpointStatus = (id: string) => {
    setEndpoints(prev => prev.map(endpoint => 
      endpoint.id === id 
        ? { ...endpoint, status: endpoint.status === 'active' ? 'maintenance' : 'active' }
        : endpoint
    ));
    
    addNotification({
      type: 'success',
      title: 'Endpoint Updated',
      message: 'Endpoint status has been changed'
    });
  };

  const revokeApiKey = (id: string) => {
    setApiKeys(prev => prev.map(key => 
      key.id === id 
        ? { ...key, status: 'revoked' as const }
        : key
    ));
    
    addNotification({
      type: 'warning',
      title: 'API Key Revoked',
      message: 'API key has been revoked and can no longer be used'
    });
  };

  const generateNewApiKey = () => {
    const newKey: APIKey = {
      id: Date.now().toString(),
      name: 'New API Key',
      key: `bz_key_${Math.random().toString(36).substr(2, 12)}...`,
      status: 'active',
      permissions: ['read:businesses'],
      requestCount: 0,
      lastUsed: new Date(),
      expiresAt: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000),
      createdAt: new Date()
    };

    setApiKeys(prev => [newKey, ...prev]);
    
    addNotification({
      type: 'success',
      title: 'API Key Generated',
      message: 'New API key has been created successfully'
    });
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'active': return 'bg-green-100 text-green-800';
      case 'deprecated': return 'bg-yellow-100 text-yellow-800';
      case 'maintenance': return 'bg-red-100 text-red-800';
      case 'revoked': return 'bg-red-100 text-red-800';
      case 'expired': return 'bg-gray-100 text-gray-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  const getMethodColor = (method: string) => {
    switch (method) {
      case 'GET': return 'bg-blue-100 text-blue-800';
      case 'POST': return 'bg-green-100 text-green-800';
      case 'PUT': return 'bg-orange-100 text-orange-800';
      case 'DELETE': return 'bg-red-100 text-red-800';
      case 'PATCH': return 'bg-purple-100 text-purple-800';
      default: return 'bg-gray-100 text-gray-800';
    }
  };

  if (user?.role !== 'admin') {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="text-center">
          <div className="text-6xl mb-4">🔒</div>
          <h2 className="text-xl font-semibold text-gray-900 mb-2">Access Denied</h2>
          <p className="text-gray-600">You need administrator privileges to access this page.</p>
        </div>
      </div>
    );
  }

  if (loading) {
    return (
      <div className="flex items-center justify-center h-64">
        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
      </div>
    );
  }

  const tabs = [
    { id: 'endpoints', label: 'API Endpoints', icon: '🔗' },
    { id: 'keys', label: 'API Keys', icon: '🔑' },
    { id: 'monitoring', label: 'Monitoring', icon: '📊' },
    { id: 'documentation', label: 'Documentation', icon: '📚' },
  ];

  return (
    <div className="max-w-7xl mx-auto space-y-6">
      {/* Header */}
      <div className="bg-white rounded-lg shadow p-6">
        <div className="flex items-center justify-between">
          <div>
            <h1 className="text-2xl font-bold text-gray-900">API Management</h1>
            <p className="text-gray-600">Monitor and manage API endpoints, keys, and documentation</p>
          </div>
          
          <div className="flex items-center space-x-3">
            <div className="relative">
              <input
                type="text"
                placeholder="Search endpoints, keys..."
                value={searchTerm}
                onChange={(e) => setSearchTerm(e.target.value)}
                className="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center">
                <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
              </div>
            </div>
            
            {activeTab === 'keys' && (
              <button
                onClick={generateNewApiKey}
                className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
              >
                Generate Key
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Tabs */}
      <div className="bg-white rounded-lg shadow">
        <div className="border-b border-gray-200">
          <nav className="flex space-x-8 px-6">
            {tabs.map((tab) => (
              <button
                key={tab.id}
                onClick={() => setActiveTab(tab.id)}
                className={`py-4 px-1 border-b-2 font-medium text-sm transition-colors ${
                  activeTab === tab.id
                    ? 'border-blue-500 text-blue-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                }`}
              >
                <span className="mr-2">{tab.icon}</span>
                {tab.label}
              </button>
            ))}
          </nav>
        </div>

        <div className="p-6">
          {/* API Endpoints Tab */}
          {activeTab === 'endpoints' && (
            <div className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div className="bg-blue-50 p-4 rounded-lg">
                  <div className="text-2xl font-bold text-blue-600">{endpoints.length}</div>
                  <div className="text-sm text-blue-600">Total Endpoints</div>
                </div>
                <div className="bg-green-50 p-4 rounded-lg">
                  <div className="text-2xl font-bold text-green-600">
                    {endpoints.filter(e => e.status === 'active').length}
                  </div>
                  <div className="text-sm text-green-600">Active</div>
                </div>
                <div className="bg-yellow-50 p-4 rounded-lg">
                  <div className="text-2xl font-bold text-yellow-600">
                    {endpoints.filter(e => e.status === 'deprecated').length}
                  </div>
                  <div className="text-sm text-yellow-600">Deprecated</div>
                </div>
                <div className="bg-red-50 p-4 rounded-lg">
                  <div className="text-2xl font-bold text-red-600">
                    {endpoints.reduce((acc, e) => acc + e.requestCount, 0).toLocaleString()}
                  </div>
                  <div className="text-sm text-red-600">Total Requests</div>
                </div>
              </div>

              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Endpoint
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Requests
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Avg Response
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Error Rate
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    {filteredEndpoints.map((endpoint) => (
                      <tr key={endpoint.id} className="hover:bg-gray-50">
                        <td className="px-6 py-4 whitespace-nowrap">
                          <div className="flex items-center">
                            <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full mr-3 ${getMethodColor(endpoint.method)}`}>
                              {endpoint.method}
                            </span>
                            <div>
                              <div className="text-sm font-medium text-gray-900">{endpoint.path}</div>
                              <div className="text-xs text-gray-500">{endpoint.description}</div>
                            </div>
                          </div>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap">
                          <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(endpoint.status)}`}>
                            {endpoint.status}
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {endpoint.requestCount.toLocaleString()}
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          {endpoint.avgResponseTime}ms
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                          <span className={`${endpoint.errorRate > 2 ? 'text-red-600' : endpoint.errorRate > 1 ? 'text-yellow-600' : 'text-green-600'}`}>
                            {endpoint.errorRate}%
                          </span>
                        </td>
                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                          <button
                            onClick={() => toggleEndpointStatus(endpoint.id)}
                            className={`text-sm px-3 py-1 rounded-md transition-colors ${
                              endpoint.status === 'active' 
                                ? 'bg-red-100 text-red-700 hover:bg-red-200' 
                                : 'bg-green-100 text-green-700 hover:bg-green-200'
                            }`}
                          >
                            {endpoint.status === 'active' ? 'Disable' : 'Enable'}
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* API Keys Tab */}
          {activeTab === 'keys' && (
            <div className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div className="bg-blue-50 p-4 rounded-lg">
                  <div className="text-2xl font-bold text-blue-600">{apiKeys.length}</div>
                  <div className="text-sm text-blue-600">Total Keys</div>
                </div>
                <div className="bg-green-50 p-4 rounded-lg">
                  <div className="text-2xl font-bold text-green-600">
                    {apiKeys.filter(k => k.status === 'active').length}
                  </div>
                  <div className="text-sm text-green-600">Active</div>
                </div>
                <div className="bg-red-50 p-4 rounded-lg">
                  <div className="text-2xl font-bold text-red-600">
                    {apiKeys.filter(k => k.status === 'revoked').length}
                  </div>
                  <div className="text-sm text-red-600">Revoked</div>
                </div>
                <div className="bg-purple-50 p-4 rounded-lg">
                  <div className="text-2xl font-bold text-purple-600">
                    {apiKeys.reduce((acc, k) => acc + k.requestCount, 0).toLocaleString()}
                  </div>
                  <div className="text-sm text-purple-600">Total Requests</div>
                </div>
              </div>

              <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {filteredApiKeys.map((key) => (
                  <div key={key.id} className="border border-gray-200 rounded-lg p-6">
                    <div className="flex items-start justify-between mb-4">
                      <div>
                        <h3 className="text-lg font-medium text-gray-900">{key.name}</h3>
                        <p className="text-sm text-gray-500 font-mono">{key.key}</p>
                      </div>
                      <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getStatusColor(key.status)}`}>
                        {key.status}
                      </span>
                    </div>

                    <div className="space-y-3">
                      <div className="flex justify-between text-sm">
                        <span className="text-gray-500">Requests:</span>
                        <span className="font-medium">{key.requestCount.toLocaleString()}</span>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-gray-500">Last Used:</span>
                        <span className="font-medium">{key.lastUsed.toLocaleDateString()}</span>
                      </div>
                      <div className="flex justify-between text-sm">
                        <span className="text-gray-500">Expires:</span>
                        <span className="font-medium">{key.expiresAt.toLocaleDateString()}</span>
                      </div>
                      
                      <div className="mt-4">
                        <div className="text-sm text-gray-500 mb-2">Permissions:</div>
                        <div className="flex flex-wrap gap-1">
                          {key.permissions.map((perm, index) => (
                            <span key={index} className="inline-flex px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                              {perm}
                            </span>
                          ))}
                        </div>
                      </div>

                      {key.status === 'active' && (
                        <div className="mt-4 pt-4 border-t border-gray-200">
                          <button
                            onClick={() => revokeApiKey(key.id)}
                            className="text-sm text-red-600 hover:text-red-800 transition-colors"
                          >
                            Revoke Key
                          </button>
                        </div>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          )}

          {/* Monitoring Tab */}
          {activeTab === 'monitoring' && (
            <div className="space-y-6">
              <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-medium text-gray-900 mb-4">Request Volume</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">Last Hour:</span>
                      <span className="font-medium">1,234 requests</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">Last 24 Hours:</span>
                      <span className="font-medium">28,945 requests</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">Last Week:</span>
                      <span className="font-medium">156,782 requests</span>
                    </div>
                  </div>
                </div>

                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-medium text-gray-900 mb-4">Response Times</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">Average:</span>
                      <span className="font-medium">245ms</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">95th Percentile:</span>
                      <span className="font-medium">1.2s</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">99th Percentile:</span>
                      <span className="font-medium">3.1s</span>
                    </div>
                  </div>
                </div>

                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-medium text-gray-900 mb-4">Error Rates</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">4xx Errors:</span>
                      <span className="font-medium text-yellow-600">1.2%</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">5xx Errors:</span>
                      <span className="font-medium text-red-600">0.3%</span>
                    </div>
                    <div className="flex justify-between">
                      <span className="text-sm text-gray-500">Success Rate:</span>
                      <span className="font-medium text-green-600">98.5%</span>
                    </div>
                  </div>
                </div>
              </div>

              <div className="bg-gray-50 rounded-lg p-8 text-center">
                <div className="text-4xl mb-4">📊</div>
                <h3 className="text-lg font-medium text-gray-900 mb-2">Advanced Monitoring</h3>
                <p className="text-gray-600 mb-4">
                  Real-time charts and detailed analytics would be displayed here
                </p>
                <div className="text-sm text-gray-500">
                  Integration with monitoring tools like Grafana, New Relic, or DataDog
                </div>
              </div>
            </div>
          )}

          {/* Documentation Tab */}
          {activeTab === 'documentation' && (
            <div className="space-y-6">
              <div className="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div className="flex items-start">
                  <div className="text-blue-600 text-2xl mr-4">📚</div>
                  <div>
                    <h3 className="text-lg font-medium text-blue-900 mb-2">API Documentation</h3>
                    <p className="text-blue-700 mb-4">
                      Interactive API documentation powered by Swagger/OpenAPI
                    </p>
                    <a 
                      href="/api/docs" 
                      target="_blank"
                      className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                      View API Docs
                      <svg className="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                    </a>
                  </div>
                </div>
              </div>

              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div className="border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-medium text-gray-900 mb-4">Quick Links</h3>
                  <div className="space-y-3">
                    <a href="#" className="block text-blue-600 hover:text-blue-800 transition-colors">
                      → Authentication Guide
                    </a>
                    <a href="#" className="block text-blue-600 hover:text-blue-800 transition-colors">
                      → Rate Limiting
                    </a>
                    <a href="#" className="block text-blue-600 hover:text-blue-800 transition-colors">
                      → Error Handling
                    </a>
                    <a href="#" className="block text-blue-600 hover:text-blue-800 transition-colors">
                      → SDK Downloads
                    </a>
                    <a href="#" className="block text-blue-600 hover:text-blue-800 transition-colors">
                      → Webhook Guide
                    </a>
                  </div>
                </div>

                <div className="border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-medium text-gray-900 mb-4">API Versions</h3>
                  <div className="space-y-3">
                    <div className="flex justify-between items-center">
                      <span>v1.0</span>
                      <span className="inline-flex px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Current</span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span>v0.9</span>
                      <span className="inline-flex px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Deprecated</span>
                    </div>
                    <div className="flex justify-between items-center">
                      <span>v0.8</span>
                      <span className="inline-flex px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Sunset</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
