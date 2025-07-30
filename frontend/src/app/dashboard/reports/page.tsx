'use client';

import { useState, useEffect } from 'react';
import ReportChart from '@/components/ReportChart';

interface ReportData {
  totalApplications: number;
  approvedApplications: number;
  pendingApplications: number;
  rejectedApplications: number;
  totalBusinesses: number;
  totalRevenue: number;
  monthlyApplications: Array<{ month: string; count: number }>;
  applicationsByType: Array<{ type: string; count: number }>;
  applicationsByStatus: Array<{ status: string; count: number }>;
  revenueByMonth: Array<{ month: string; revenue: number }>;
  processingTime: Array<{ range: string; count: number }>;
  regionData: Array<{ region: string; applications: number; revenue: number }>;
  userActivity: Array<{ date: string; activeUsers: number; newRegistrations: number }>;
}

interface ReportFilters {
  timeRange: string;
  region: string;
  applicationType: string;
  status: string;
  dateFrom: string;
  dateTo: string;
}

export default function ReportsPage() {
  const [reportData, setReportData] = useState<ReportData | null>(null);
  const [filters, setFilters] = useState<ReportFilters>({
    timeRange: '30days',
    region: 'all',
    applicationType: 'all',
    status: 'all',
    dateFrom: '',
    dateTo: ''
  });
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState('overview');
  const [exportModalOpen, setExportModalOpen] = useState(false);
  const [selectedCharts, setSelectedCharts] = useState<string[]>(['applications', 'revenue']);

  useEffect(() => {
    const fetchReportData = async () => {
      try {
        setLoading(true);
        // Simulate API call with filters
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        setReportData({
          totalApplications: 1247,
          approvedApplications: 892,
          pendingApplications: 245,
          rejectedApplications: 110,
          totalBusinesses: 756,
          totalRevenue: 1250000000,
          monthlyApplications: [
            { month: 'Jan', count: 85 },
            { month: 'Feb', count: 92 },
            { month: 'Mar', count: 108 },
            { month: 'Apr', count: 125 },
            { month: 'May', count: 145 },
            { month: 'Jun', count: 162 },
            { month: 'Jul', count: 178 },
            { month: 'Aug', count: 195 },
            { month: 'Sep', count: 186 },
            { month: 'Oct', count: 171 },
          ],
          applicationsByType: [
            { type: 'SIUP', count: 425 },
            { type: 'TDP', count: 378 },
            { type: 'NIB', count: 289 },
            { type: 'IUMK', count: 155 }
          ],
          applicationsByStatus: [
            { status: 'Approved', count: 892 },
            { status: 'Pending', count: 245 },
            { status: 'Rejected', count: 110 }
          ],
          revenueByMonth: [
            { month: 'Jan', revenue: 85000000 },
            { month: 'Feb', revenue: 92000000 },
            { month: 'Mar', revenue: 108000000 },
            { month: 'Apr', revenue: 125000000 },
            { month: 'May', revenue: 145000000 },
            { month: 'Jun', revenue: 162000000 },
            { month: 'Jul', revenue: 178000000 },
            { month: 'Aug', revenue: 195000000 },
            { month: 'Sep', revenue: 186000000 },
            { month: 'Oct', revenue: 171000000 },
          ],
          processingTime: [
            { range: '< 1 day', count: 245 },
            { range: '1-3 days', count: 456 },
            { range: '4-7 days', count: 389 },
            { range: '> 7 days', count: 157 }
          ],
          regionData: [
            { region: 'Jakarta', applications: 342, revenue: 425000000 },
            { region: 'Surabaya', applications: 298, revenue: 365000000 },
            { region: 'Bandung', applications: 234, revenue: 298000000 },
            { region: 'Medan', applications: 189, revenue: 235000000 },
            { region: 'Semarang', applications: 184, revenue: 227000000 }
          ],
          userActivity: [
            { date: '2024-01-01', activeUsers: 1245, newRegistrations: 45 },
            { date: '2024-01-02', activeUsers: 1298, newRegistrations: 52 },
            { date: '2024-01-03', activeUsers: 1156, newRegistrations: 38 },
            { date: '2024-01-04', activeUsers: 1387, newRegistrations: 64 },
            { date: '2024-01-05', activeUsers: 1425, newRegistrations: 71 }
          ]
        });
      } catch (error) {
        console.error('Error fetching report data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchReportData();
  }, [filters]);

  const handleFilterChange = (key: keyof ReportFilters, value: string) => {
    setFilters(prev => ({
      ...prev,
      [key]: value
    }));
  };

  const exportData = (format: 'csv' | 'pdf' | 'excel') => {
    // Simulate export functionality
    console.log(`Exporting data in ${format} format with charts:`, selectedCharts);
    
    // In a real app, this would trigger a download
    const exportData = {
      filters,
      charts: selectedCharts,
      data: reportData,
      timestamp: new Date().toISOString()
    };
    
    // Simulate file download
    const blob = new Blob([JSON.stringify(exportData, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `bizmark-report-${new Date().toISOString().split('T')[0]}.${format}`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    setExportModalOpen(false);
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount);
  };

  const transformDataForChart = (data: Record<string, string | number>[], xKey: string, yKey: string, type: string) => {
    const colors = {
      blue: '#3B82F6',
      green: '#10B981',
      yellow: '#F59E0B',
      purple: '#8B5CF6',
      red: '#EF4444',
      indigo: '#6366F1'
    };

    const backgroundColors = [
      colors.blue,
      colors.green,
      colors.yellow,
      colors.purple,
      colors.red,
      colors.indigo
    ];

    return {
      labels: data.map(item => String(item[xKey])),
      datasets: [{
        label: yKey === 'revenue' ? 'Revenue (IDR)' : 'Count',
        data: data.map(item => Number(item[yKey])),
        backgroundColor: type === 'pie' || type === 'doughnut' ? 
          backgroundColors.slice(0, data.length) : 
          colors.blue,
        borderColor: colors.blue,
        tension: 0.4
      }]
    };
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">Loading reports...</p>
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
              <h1 className="text-3xl font-bold text-gray-900">Advanced Reports & Analytics</h1>
              <p className="mt-2 text-gray-600">Comprehensive insights into your UMKM licensing operations</p>
            </div>
            <div className="flex space-x-3">
              <button
                onClick={() => setExportModalOpen(true)}
                className="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center"
              >
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Export Report
              </button>
              <button
                onClick={() => window.location.reload()}
                className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
              >
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh
              </button>
            </div>
          </div>
        </div>

        {/* Advanced Filters */}
        <div className="bg-white rounded-lg shadow-sm p-6 mb-8">
          <h3 className="text-lg font-semibold text-gray-900 mb-4">Report Filters</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Time Range</label>
              <select
                value={filters.timeRange}
                onChange={(e) => handleFilterChange('timeRange', e.target.value)}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="7days">Last 7 days</option>
                <option value="30days">Last 30 days</option>
                <option value="90days">Last 3 months</option>
                <option value="365days">Last year</option>
                <option value="custom">Custom range</option>
              </select>
            </div>
            
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Region</label>
              <select
                value={filters.region}
                onChange={(e) => handleFilterChange('region', e.target.value)}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="all">All Regions</option>
                <option value="jakarta">Jakarta</option>
                <option value="surabaya">Surabaya</option>
                <option value="bandung">Bandung</option>
                <option value="medan">Medan</option>
                <option value="semarang">Semarang</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Application Type</label>
              <select
                value={filters.applicationType}
                onChange={(e) => handleFilterChange('applicationType', e.target.value)}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="all">All Types</option>
                <option value="siup">SIUP</option>
                <option value="tdp">TDP</option>
                <option value="nib">NIB</option>
                <option value="iumk">IUMK</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select
                value={filters.status}
                onChange={(e) => handleFilterChange('status', e.target.value)}
                className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="all">All Status</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>

            {filters.timeRange === 'custom' && (
              <>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                  <input
                    type="date"
                    value={filters.dateFrom}
                    onChange={(e) => handleFilterChange('dateFrom', e.target.value)}
                    className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                  <input
                    type="date"
                    value={filters.dateTo}
                    onChange={(e) => handleFilterChange('dateTo', e.target.value)}
                    className="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
              </>
            )}
          </div>
        </div>

        {/* Tabs */}
        <div className="bg-white rounded-lg shadow-sm mb-8">
          <div className="border-b border-gray-200">
            <nav className="-mb-px flex space-x-8 px-6">
              {[
                { id: 'overview', name: 'Overview' },
                { id: 'applications', name: 'Applications' },
                { id: 'revenue', name: 'Revenue' },
                { id: 'performance', name: 'Performance' },
                { id: 'regional', name: 'Regional Analysis' }
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
            {activeTab === 'overview' && reportData && (
              <div className="space-y-8">
                {/* Key Metrics Cards */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                  <div className="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
                    <h3 className="text-lg font-semibold mb-2">Total Applications</h3>
                    <p className="text-3xl font-bold">{reportData.totalApplications.toLocaleString()}</p>
                    <p className="text-blue-100 text-sm mt-2">
                      +12% from last month
                    </p>
                  </div>
                  
                  <div className="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
                    <h3 className="text-lg font-semibold mb-2">Approved Applications</h3>
                    <p className="text-3xl font-bold">{reportData.approvedApplications.toLocaleString()}</p>
                    <p className="text-green-100 text-sm mt-2">
                      {((reportData.approvedApplications / reportData.totalApplications) * 100).toFixed(1)}% approval rate
                    </p>
                  </div>
                  
                  <div className="bg-gradient-to-r from-yellow-500 to-orange-500 rounded-lg p-6 text-white">
                    <h3 className="text-lg font-semibold mb-2">Pending Applications</h3>
                    <p className="text-3xl font-bold">{reportData.pendingApplications.toLocaleString()}</p>
                    <p className="text-orange-100 text-sm mt-2">
                      Avg. processing: 3.2 days
                    </p>
                  </div>
                  
                  <div className="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
                    <h3 className="text-lg font-semibold mb-2">Total Revenue</h3>
                    <p className="text-3xl font-bold">{formatCurrency(reportData.totalRevenue)}</p>
                    <p className="text-purple-100 text-sm mt-2">
                      +18% from last month
                    </p>
                  </div>
                </div>

                {/* Charts Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">Monthly Applications Trend</h3>
                    <ReportChart 
                      data={transformDataForChart(reportData.monthlyApplications, 'month', 'count', 'line')}
                      type="line"
                    />
                  </div>
                  
                  <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">Applications by Status</h3>
                    <ReportChart 
                      data={transformDataForChart(reportData.applicationsByStatus, 'status', 'count', 'pie')}
                      type="pie"
                    />
                  </div>
                  
                  <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">Revenue Trend</h3>
                    <ReportChart 
                      data={transformDataForChart(reportData.revenueByMonth, 'month', 'revenue', 'bar')}
                      type="bar"
                    />
                  </div>
                  
                  <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">Applications by Type</h3>
                    <ReportChart 
                      data={transformDataForChart(reportData.applicationsByType, 'type', 'count', 'doughnut')}
                      type="doughnut"
                    />
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'performance' && reportData && (
              <div className="space-y-8">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">Processing Time Distribution</h3>
                    <ReportChart 
                      data={transformDataForChart(reportData.processingTime, 'range', 'count', 'bar')}
                      type="bar"
                    />
                  </div>
                  
                  <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">User Activity Trend</h3>
                    <ReportChart 
                      data={transformDataForChart(reportData.userActivity, 'date', 'activeUsers', 'line')}
                      type="line"
                    />
                  </div>
                </div>

                {/* Performance Metrics Table */}
                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-semibold mb-4">Key Performance Indicators</h3>
                  <div className="overflow-x-auto">
                    <table className="w-full table-auto">
                      <thead>
                        <tr className="border-b border-gray-200">
                          <th className="text-left py-3 px-4 font-semibold text-gray-900">Metric</th>
                          <th className="text-left py-3 px-4 font-semibold text-gray-900">Current Period</th>
                          <th className="text-left py-3 px-4 font-semibold text-gray-900">Previous Period</th>
                          <th className="text-left py-3 px-4 font-semibold text-gray-900">Change</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr className="border-b border-gray-100">
                          <td className="py-3 px-4">Average Processing Time</td>
                          <td className="py-3 px-4">3.2 days</td>
                          <td className="py-3 px-4">4.1 days</td>
                          <td className="py-3 px-4 text-green-600">-22%</td>
                        </tr>
                        <tr className="border-b border-gray-100">
                          <td className="py-3 px-4">Approval Rate</td>
                          <td className="py-3 px-4">71.5%</td>
                          <td className="py-3 px-4">68.2%</td>
                          <td className="py-3 px-4 text-green-600">+3.3%</td>
                        </tr>
                        <tr className="border-b border-gray-100">
                          <td className="py-3 px-4">User Satisfaction</td>
                          <td className="py-3 px-4">4.2/5.0</td>
                          <td className="py-3 px-4">3.9/5.0</td>
                          <td className="py-3 px-4 text-green-600">+7.7%</td>
                        </tr>
                        <tr>
                          <td className="py-3 px-4">System Uptime</td>
                          <td className="py-3 px-4">99.8%</td>
                          <td className="py-3 px-4">99.6%</td>
                          <td className="py-3 px-4 text-green-600">+0.2%</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            )}

            {activeTab === 'regional' && reportData && (
              <div className="space-y-8">
                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-semibold mb-4">Regional Performance</h3>
                  <div className="overflow-x-auto">
                    <table className="w-full table-auto">
                      <thead>
                        <tr className="border-b border-gray-200">
                          <th className="text-left py-3 px-4 font-semibold text-gray-900">Region</th>
                          <th className="text-left py-3 px-4 font-semibold text-gray-900">Applications</th>
                          <th className="text-left py-3 px-4 font-semibold text-gray-900">Revenue</th>
                          <th className="text-left py-3 px-4 font-semibold text-gray-900">Avg. Revenue per App</th>
                        </tr>
                      </thead>
                      <tbody>
                        {reportData.regionData.map((region, index) => (
                          <tr key={index} className="border-b border-gray-100">
                            <td className="py-3 px-4 font-medium">{region.region}</td>
                            <td className="py-3 px-4">{region.applications.toLocaleString()}</td>
                            <td className="py-3 px-4">{formatCurrency(region.revenue)}</td>
                            <td className="py-3 px-4">{formatCurrency(region.revenue / region.applications)}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            )}
          </div>
        </div>

        {/* Export Modal */}
        {exportModalOpen && (
          <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div className="bg-white rounded-lg p-6 w-full max-w-md">
              <div className="flex justify-between items-center mb-4">
                <h3 className="text-lg font-semibold">Export Report</h3>
                <button
                  onClick={() => setExportModalOpen(false)}
                  className="text-gray-400 hover:text-gray-600"
                >
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>

              <div className="space-y-4">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">Include Charts</label>
                  <div className="space-y-2">
                    {['applications', 'revenue', 'status', 'types', 'performance'].map((chart) => (
                      <label key={chart} className="flex items-center">
                        <input
                          type="checkbox"
                          checked={selectedCharts.includes(chart)}
                          onChange={(e) => {
                            if (e.target.checked) {
                              setSelectedCharts([...selectedCharts, chart]);
                            } else {
                              setSelectedCharts(selectedCharts.filter(c => c !== chart));
                            }
                          }}
                          className="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                        />
                        <span className="text-sm text-gray-700 capitalize">{chart}</span>
                      </label>
                    ))}
                  </div>
                </div>

                <div className="flex space-x-3">
                  <button
                    onClick={() => exportData('pdf')}
                    className="flex-1 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition-colors"
                  >
                    Export PDF
                  </button>
                  <button
                    onClick={() => exportData('excel')}
                    className="flex-1 bg-green-600 text-white py-2 px-4 rounded-lg hover:bg-green-700 transition-colors"
                  >
                    Export Excel
                  </button>
                  <button
                    onClick={() => exportData('csv')}
                    className="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors"
                  >
                    Export CSV
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
