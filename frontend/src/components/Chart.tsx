'use client';

import { useEffect, useState } from 'react';
import { dashboardService } from '@/services/dashboard.service';
import { ChartData } from '@/types';

interface ChartProps {
  type: 'license' | 'application' | 'trend';
  title: string;
  className?: string;
}

export default function Chart({ type, title, className = '' }: ChartProps) {
  const [chartData, setChartData] = useState<ChartData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  useEffect(() => {
    const fetchChartData = async () => {
      try {
        let response;
        switch (type) {
          case 'license':
            response = await dashboardService.getLicenseStats();
            break;
          case 'application':
            response = await dashboardService.getApplicationStats();
            break;
          case 'trend':
            response = await dashboardService.getApplicationTrends(6);
            break;
          default:
            throw new Error('Invalid chart type');
        }
        setChartData(response.data);
      } catch (error) {
        console.error('Failed to fetch chart data:', error);
        setError('Gagal memuat data chart');
      } finally {
        setLoading(false);
      }
    };

    fetchChartData();
  }, [type]);

  if (loading) {
    return (
      <div className={`bg-white rounded-lg shadow p-6 ${className}`}>
        <h3 className="text-lg font-semibold text-gray-900 mb-4">{title}</h3>
        <div className="flex justify-center items-center h-64">
          <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        </div>
      </div>
    );
  }

  if (error || !chartData) {
    return (
      <div className={`bg-white rounded-lg shadow p-6 ${className}`}>
        <h3 className="text-lg font-semibold text-gray-900 mb-4">{title}</h3>
        <div className="flex flex-col items-center justify-center h-64 text-gray-500">
          <svg className="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p>{error || 'Data tidak tersedia'}</p>
        </div>
      </div>
    );
  }

  // Simple bar chart for license/application stats
  if (type === 'license' || type === 'application') {
    const maxValue = Math.max(...chartData.datasets[0].data);
    
    return (
      <div className={`bg-white rounded-lg shadow p-6 ${className}`}>
        <h3 className="text-lg font-semibold text-gray-900 mb-4">{title}</h3>
        <div className="space-y-4">
          {chartData.labels.map((label, index) => {
            const value = chartData.datasets[0].data[index];
            const percentage = maxValue > 0 ? (value / maxValue) * 100 : 0;
            
            return (
              <div key={label} className="flex items-center">
                <div className="w-24 text-sm text-gray-600 truncate">{label}</div>
                <div className="flex-1 mx-4">
                  <div className="w-full bg-gray-200 rounded-full h-3">
                    <div
                      className="bg-blue-600 h-3 rounded-full transition-all duration-500"
                      style={{ width: `${percentage}%` }}
                    ></div>
                  </div>
                </div>
                <div className="w-12 text-right text-sm font-medium text-gray-900">{value}</div>
              </div>
            );
          })}
        </div>
      </div>
    );
  }

  // Line chart for trends (simplified as a list)
  if (type === 'trend') {
    return (
      <div className={`bg-white rounded-lg shadow p-6 ${className}`}>
        <h3 className="text-lg font-semibold text-gray-900 mb-4">{title}</h3>
        <div className="space-y-3">
          {chartData.labels.map((label, index) => {
            const value = chartData.datasets[0].data[index];
            const prevValue = index > 0 ? chartData.datasets[0].data[index - 1] : value;
            const trend = value > prevValue ? 'up' : value < prevValue ? 'down' : 'same';
            
            return (
              <div key={label} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                  <div className="font-medium text-gray-900">{label}</div>
                  <div className="text-sm text-gray-500">Total Aplikasi</div>
                </div>
                <div className="flex items-center space-x-2">
                  <span className="text-2xl font-bold text-gray-900">{value}</span>
                  {trend === 'up' && (
                    <svg className="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 14l9-9 3 3" />
                    </svg>
                  )}
                  {trend === 'down' && (
                    <svg className="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 14l-9-9-3 3" />
                    </svg>
                  )}
                </div>
              </div>
            );
          })}
        </div>
      </div>
    );
  }

  return null;
}
