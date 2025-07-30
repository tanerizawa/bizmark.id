'use client';

import { useState, useEffect } from 'react';

interface BIMetrics {
  businessGrowth: {
    totalBusinesses: number;
    newBusinessesThisMonth: number;
    growthRate: number;
    projectedGrowth: number;
  };
  applicationInsights: {
    totalApplications: number;
    avgProcessingTime: number;
    approvalRate: number;
    bottlenecks: string[];
  };
  financialMetrics: {
    totalRevenue: number;
    monthlyRecurringRevenue: number;
    averageRevenuePerUser: number;
    revenueGrowthRate: number;
  };
  userEngagement: {
    activeUsers: number;
    userRetentionRate: number;
    sessionDuration: number;
    userSatisfactionScore: number;
  };
  operationalEfficiency: {
    systemUptime: number;
    averageResponseTime: number;
    errorRate: number;
    staffProductivity: number;
  };
}

interface Prediction {
  metric: string;
  currentValue: number;
  predictedValue: number;
  confidence: number;
  timeframe: string;
  trend: 'up' | 'down' | 'stable';
}

interface MarketInsight {
  id: string;
  category: 'market_trend' | 'competitor_analysis' | 'customer_behavior' | 'regulatory_change';
  title: string;
  description: string;
  impact: 'low' | 'medium' | 'high' | 'critical';
  actionRequired: boolean;
  recommendation: string;
  timestamp: string;
}

export default function BusinessIntelligencePage() {
  const [metrics, setMetrics] = useState<BIMetrics | null>(null);
  const [predictions, setPredictions] = useState<Prediction[]>([]);
  const [marketInsights, setMarketInsights] = useState<MarketInsight[]>([]);
  const [loading, setLoading] = useState(true);
  const [activeView, setActiveView] = useState('overview');
  const [timeRange, setTimeRange] = useState('30days');

  useEffect(() => {
    const fetchBIData = async () => {
      try {
        setLoading(true);
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        setMetrics({
          businessGrowth: {
            totalBusinesses: 15684,
            newBusinessesThisMonth: 342,
            growthRate: 12.5,
            projectedGrowth: 18.2
          },
          applicationInsights: {
            totalApplications: 23456,
            avgProcessingTime: 2.8,
            approvalRate: 84.5,
            bottlenecks: ['Document Verification', 'Manual Review Process', 'Third-party API Delays']
          },
          financialMetrics: {
            totalRevenue: 2450000000,
            monthlyRecurringRevenue: 245000000,
            averageRevenuePerUser: 156000,
            revenueGrowthRate: 15.8
          },
          userEngagement: {
            activeUsers: 8945,
            userRetentionRate: 78.5,
            sessionDuration: 24.5,
            userSatisfactionScore: 4.2
          },
          operationalEfficiency: {
            systemUptime: 99.8,
            averageResponseTime: 245,
            errorRate: 0.02,
            staffProductivity: 87.3
          }
        });

        setPredictions([
          {
            metric: 'Monthly Applications',
            currentValue: 1856,
            predictedValue: 2150,
            confidence: 87,
            timeframe: 'Next 30 days',
            trend: 'up'
          },
          {
            metric: 'Revenue',
            currentValue: 245000000,
            predictedValue: 290000000,
            confidence: 82,
            timeframe: 'Next quarter',
            trend: 'up'
          },
          {
            metric: 'Processing Time',
            currentValue: 2.8,
            predictedValue: 2.2,
            confidence: 74,
            timeframe: 'Next 60 days',
            trend: 'down'
          },
          {
            metric: 'User Satisfaction',
            currentValue: 4.2,
            predictedValue: 4.6,
            confidence: 91,
            timeframe: 'Next quarter',
            trend: 'up'
          }
        ]);

        setMarketInsights([
          {
            id: '1',
            category: 'market_trend',
            title: 'Increasing Demand for Digital Business Registration',
            description: 'Market analysis shows 35% increase in digital-first business registration preferences among SMEs',
            impact: 'high',
            actionRequired: true,
            recommendation: 'Accelerate mobile app development and enhance digital onboarding experience',
            timestamp: '2025-07-30T08:00:00Z'
          },
          {
            id: '2',
            category: 'regulatory_change',
            title: 'New OSS Regulation Updates',
            description: 'Government announced new streamlined processes for business licensing, affecting our compliance requirements',
            impact: 'critical',
            actionRequired: true,
            recommendation: 'Update system workflows to align with new OSS requirements by Q4 2025',
            timestamp: '2025-07-29T14:30:00Z'
          },
          {
            id: '3',
            category: 'customer_behavior',
            title: 'Peak Application Submission Hours',
            description: 'Data shows 60% of applications submitted between 9-11 AM, causing system load issues',
            impact: 'medium',
            actionRequired: true,
            recommendation: 'Implement load balancing and consider incentivizing off-peak submissions',
            timestamp: '2025-07-28T16:45:00Z'
          }
        ]);

      } catch (error) {
        console.error('Error fetching BI data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchBIData();
  }, [timeRange]);

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    }).format(amount);
  };

  const getImpactColor = (impact: string) => {
    switch (impact) {
      case 'low': return 'text-green-600 bg-green-100';
      case 'medium': return 'text-yellow-600 bg-yellow-100';
      case 'high': return 'text-orange-600 bg-orange-100';
      case 'critical': return 'text-red-600 bg-red-100';
      default: return 'text-gray-600 bg-gray-100';
    }
  };

  const getTrendIcon = (trend: string) => {
    switch (trend) {
      case 'up':
        return (
          <svg className="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 17l9.2-9.2M17 17V7H7" />
          </svg>
        );
      case 'down':
        return (
          <svg className="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 7l-9.2 9.2M7 7v10h10" />
          </svg>
        );
      default:
        return (
          <svg className="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 12H4" />
          </svg>
        );
    }
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
          <p className="mt-4 text-gray-600">Loading business intelligence...</p>
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
              <h1 className="text-3xl font-bold text-gray-900">Business Intelligence Dashboard</h1>
              <p className="mt-2 text-gray-600">AI-powered insights and predictive analytics for strategic decision making</p>
            </div>
            <div className="flex space-x-3">
              <select
                value={timeRange}
                onChange={(e) => setTimeRange(e.target.value)}
                className="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="7days">Last 7 days</option>
                <option value="30days">Last 30 days</option>
                <option value="90days">Last 3 months</option>
                <option value="365days">Last year</option>
              </select>
              <button className="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <svg className="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Refresh Data
              </button>
            </div>
          </div>
        </div>

        {/* Key Metrics Overview */}
        {metrics && (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <div className="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <h3 className="text-sm font-medium opacity-90">Total Businesses</h3>
                  <p className="text-2xl font-bold">{metrics.businessGrowth.totalBusinesses.toLocaleString()}</p>
                  <p className="text-xs opacity-75 mt-1">+{metrics.businessGrowth.growthRate}% growth</p>
                </div>
                <div className="p-2 bg-white bg-opacity-20 rounded-lg">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 10V3L4 14h7v7l9-11h-7z" />
                  </svg>
                </div>
              </div>
            </div>

            <div className="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <h3 className="text-sm font-medium opacity-90">Revenue</h3>
                  <p className="text-2xl font-bold">{formatCurrency(metrics.financialMetrics.totalRevenue)}</p>
                  <p className="text-xs opacity-75 mt-1">+{metrics.financialMetrics.revenueGrowthRate}% growth</p>
                </div>
                <div className="p-2 bg-white bg-opacity-20 rounded-lg">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                  </svg>
                </div>
              </div>
            </div>

            <div className="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <h3 className="text-sm font-medium opacity-90">Approval Rate</h3>
                  <p className="text-2xl font-bold">{metrics.applicationInsights.approvalRate}%</p>
                  <p className="text-xs opacity-75 mt-1">Processing excellence</p>
                </div>
                <div className="p-2 bg-white bg-opacity-20 rounded-lg">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <div className="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg p-6 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <h3 className="text-sm font-medium opacity-90">Active Users</h3>
                  <p className="text-2xl font-bold">{metrics.userEngagement.activeUsers.toLocaleString()}</p>
                  <p className="text-xs opacity-75 mt-1">{metrics.userEngagement.userRetentionRate}% retention</p>
                </div>
                <div className="p-2 bg-white bg-opacity-20 rounded-lg">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                  </svg>
                </div>
              </div>
            </div>

            <div className="bg-gradient-to-r from-teal-500 to-cyan-600 rounded-lg p-6 text-white">
              <div className="flex items-center justify-between">
                <div>
                  <h3 className="text-sm font-medium opacity-90">System Uptime</h3>
                  <p className="text-2xl font-bold">{metrics.operationalEfficiency.systemUptime}%</p>
                  <p className="text-xs opacity-75 mt-1">Operational excellence</p>
                </div>
                <div className="p-2 bg-white bg-opacity-20 rounded-lg">
                  <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                  </svg>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Navigation Tabs */}
        <div className="bg-white rounded-lg shadow-sm mb-8">
          <div className="border-b border-gray-200">
            <nav className="-mb-px flex space-x-8 px-6">
              {[
                { id: 'overview', name: 'Overview', icon: '📊' },
                { id: 'predictions', name: 'AI Predictions', icon: '🔮' },
                { id: 'insights', name: 'Market Insights', icon: '💡' },
                { id: 'optimization', name: 'Process Optimization', icon: '⚡' }
              ].map((tab) => (
                <button
                  key={tab.id}
                  onClick={() => setActiveView(tab.id)}
                  className={`py-4 px-1 border-b-2 font-medium text-sm flex items-center space-x-2 ${
                    activeView === tab.id
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  <span>{tab.icon}</span>
                  <span>{tab.name}</span>
                </button>
              ))}
            </nav>
          </div>

          <div className="p-6">
            {activeView === 'overview' && metrics && (
              <div className="space-y-8">
                {/* Business Growth Analysis */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                  <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">Business Growth Metrics</h3>
                    <div className="space-y-4">
                      <div className="flex justify-between items-center">
                        <span className="text-gray-600">New Businesses This Month</span>
                        <span className="font-semibold">{metrics.businessGrowth.newBusinessesThisMonth}</span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-gray-600">Growth Rate</span>
                        <span className="font-semibold text-green-600">+{metrics.businessGrowth.growthRate}%</span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-gray-600">Projected Growth</span>
                        <span className="font-semibold text-blue-600">+{metrics.businessGrowth.projectedGrowth}%</span>
                      </div>
                    </div>
                  </div>

                  <div className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold mb-4">Financial Performance</h3>
                    <div className="space-y-4">
                      <div className="flex justify-between items-center">
                        <span className="text-gray-600">Monthly Recurring Revenue</span>
                        <span className="font-semibold">{formatCurrency(metrics.financialMetrics.monthlyRecurringRevenue)}</span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-gray-600">Average Revenue Per User</span>
                        <span className="font-semibold">{formatCurrency(metrics.financialMetrics.averageRevenuePerUser)}</span>
                      </div>
                      <div className="flex justify-between items-center">
                        <span className="text-gray-600">Revenue Growth Rate</span>
                        <span className="font-semibold text-green-600">+{metrics.financialMetrics.revenueGrowthRate}%</span>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Process Bottlenecks */}
                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-semibold mb-4">Identified Process Bottlenecks</h3>
                  <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {metrics.applicationInsights.bottlenecks.map((bottleneck, index) => (
                      <div key={index} className="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div className="flex items-center">
                          <div className="p-2 bg-red-100 rounded-lg mr-3">
                            <svg className="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                          </div>
                          <div>
                            <h4 className="font-medium text-gray-900">{bottleneck}</h4>
                            <p className="text-sm text-gray-600">Requires optimization</p>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                </div>
              </div>
            )}

            {activeView === 'predictions' && (
              <div className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  {predictions.map((prediction, index) => (
                    <div key={index} className="bg-white border border-gray-200 rounded-lg p-6">
                      <div className="flex items-center justify-between mb-4">
                        <h3 className="text-lg font-semibold">{prediction.metric}</h3>
                        <div className="flex items-center">
                          {getTrendIcon(prediction.trend)}
                          <span className="ml-2 text-sm text-gray-500">{prediction.confidence}% confidence</span>
                        </div>
                      </div>
                      
                      <div className="space-y-3">
                        <div className="flex justify-between">
                          <span className="text-gray-600">Current:</span>
                          <span className="font-semibold">
                            {prediction.metric.toLowerCase().includes('revenue') 
                              ? formatCurrency(prediction.currentValue)
                              : prediction.currentValue.toLocaleString()
                            }
                          </span>
                        </div>
                        <div className="flex justify-between">
                          <span className="text-gray-600">Predicted:</span>
                          <span className="font-semibold text-blue-600">
                            {prediction.metric.toLowerCase().includes('revenue') 
                              ? formatCurrency(prediction.predictedValue)
                              : prediction.predictedValue.toLocaleString()
                            }
                          </span>
                        </div>
                        <div className="flex justify-between">
                          <span className="text-gray-600">Timeframe:</span>
                          <span className="text-sm text-gray-500">{prediction.timeframe}</span>
                        </div>
                      </div>

                      <div className="mt-4 bg-gray-100 rounded-full h-2">
                        <div 
                          className="bg-blue-600 h-2 rounded-full" 
                          style={{ width: `${prediction.confidence}%` }}
                        ></div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            )}

            {activeView === 'insights' && (
              <div className="space-y-6">
                {marketInsights.map((insight) => (
                  <div key={insight.id} className="bg-white border border-gray-200 rounded-lg p-6">
                    <div className="flex items-start justify-between mb-4">
                      <div className="flex-1">
                        <div className="flex items-center space-x-3 mb-2">
                          <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getImpactColor(insight.impact)}`}>
                            {insight.impact} impact
                          </span>
                          <span className="text-sm text-gray-500">
                            {new Date(insight.timestamp).toLocaleDateString()}
                          </span>
                          {insight.actionRequired && (
                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-orange-700 bg-orange-100">
                              Action Required
                            </span>
                          )}
                        </div>
                        <h3 className="text-lg font-semibold text-gray-900 mb-2">{insight.title}</h3>
                        <p className="text-gray-600 mb-4">{insight.description}</p>
                        <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                          <h4 className="text-sm font-medium text-blue-900 mb-1">💡 Recommendation:</h4>
                          <p className="text-sm text-blue-800">{insight.recommendation}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}

            {activeView === 'optimization' && metrics && (
              <div className="space-y-6">
                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-semibold mb-4">Performance Optimization Opportunities</h3>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-4">
                      <h4 className="font-medium text-gray-900">System Performance</h4>
                      <div className="space-y-2">
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-gray-600">Response Time</span>
                          <span className="text-sm font-medium">{metrics.operationalEfficiency.averageResponseTime}ms</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-gray-600">Error Rate</span>
                          <span className="text-sm font-medium">{metrics.operationalEfficiency.errorRate}%</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-gray-600">Staff Productivity</span>
                          <span className="text-sm font-medium">{metrics.operationalEfficiency.staffProductivity}%</span>
                        </div>
                      </div>
                    </div>

                    <div className="space-y-4">
                      <h4 className="font-medium text-gray-900">User Experience</h4>
                      <div className="space-y-2">
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-gray-600">Session Duration</span>
                          <span className="text-sm font-medium">{metrics.userEngagement.sessionDuration} min</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-gray-600">Satisfaction Score</span>
                          <span className="text-sm font-medium">{metrics.userEngagement.userSatisfactionScore}/5.0</span>
                        </div>
                        <div className="flex justify-between items-center">
                          <span className="text-sm text-gray-600">Avg Processing Time</span>
                          <span className="text-sm font-medium">{metrics.applicationInsights.avgProcessingTime} days</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="bg-white border border-gray-200 rounded-lg p-6">
                  <h3 className="text-lg font-semibold mb-4">Recommended Actions</h3>
                  <div className="space-y-4">
                    <div className="bg-green-50 border border-green-200 rounded-lg p-4">
                      <h4 className="font-medium text-green-900">🚀 Quick Wins</h4>
                      <ul className="mt-2 text-sm text-green-800 space-y-1">
                        <li>• Implement caching to reduce response times by 30%</li>
                        <li>• Add progress indicators to improve user satisfaction</li>
                        <li>• Automate document verification to reduce processing time</li>
                      </ul>
                    </div>
                    
                    <div className="bg-blue-50 border border-blue-200 rounded-lg p-4">
                      <h4 className="font-medium text-blue-900">📈 Strategic Improvements</h4>
                      <ul className="mt-2 text-sm text-blue-800 space-y-1">
                        <li>• Develop mobile app to capture 40% more market share</li>
                        <li>• Implement AI-powered application review system</li>
                        <li>• Create self-service portal to reduce support load</li>
                      </ul>
                    </div>
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
