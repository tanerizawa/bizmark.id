/**
 * Advanced Search Dashboard
 * Enhanced search interface with filters, facets, and intelligent ranking
 */

'use client';

import React, { useState } from 'react';
import { 
  Search, 
  Filter, 
  SlidersHorizontal, 
  Clock, 
  TrendingUp, 
  FileText,
  Star,
  X,
  Loader2,
  ArrowUpDown
} from 'lucide-react';
import { 
  useAdvancedSearch, 
  useSearchSuggestions, 
  usePopularSearches,
  useSearchFilters,
  useSearchHistory
} from '@/hooks/useAdvancedSearch';
import { SearchFilters } from '@/services/advancedSearch';

export default function AdvancedSearchPage() {
  const [activeTab, setActiveTab] = useState<'search' | 'results' | 'filters' | 'analytics'>('search');
  const [showFilters, setShowFilters] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');

  // Search hooks
  const {
    query,
    results,
    facets,
    loading,
    error,
    pagination,
    processingTime,
    search,
    setQuery,
    hasResults,
    isEmpty,
    loadMore,
    hasMore
  } = useAdvancedSearch();

  const { suggestions, loading: suggestionsLoading } = useSearchSuggestions(searchQuery);
  const { popularSearches } = usePopularSearches();
  const { 
    filters, 
    toggleFilter, 
    clearAllFilters, 
    isFilterActive, 
    activeFilterCount,
    hasActiveFilters 
  } = useSearchFilters();
  const { history, addToHistory, removeFromHistory, clearHistory } = useSearchHistory();

  // Handle search submission
  const handleSearch = async (searchTerm?: string) => {
    const term = searchTerm || searchQuery;
    if (term.trim()) {
      setQuery(term);
      addToHistory(term);
      await search(term, filters);
      setActiveTab('results');
    }
  };

  // Handle filter changes
  const handleFilterChange = (field: keyof SearchFilters, value: string) => {
    toggleFilter(field, value);
    if (query) {
      search(query, { ...filters, [field]: isFilterActive(field, value) ? 
        (filters[field] as string[])?.filter(v => v !== value) : 
        [...(filters[field] as string[] || []), value] 
      });
    }
  };

  const tabs = [
    { key: 'search', label: 'Pencarian', icon: Search },
    { key: 'results', label: `Hasil (${pagination.total})`, icon: FileText },
    { key: 'filters', label: `Filter (${activeFilterCount})`, icon: Filter },
    { key: 'analytics', label: 'Analytics', icon: TrendingUp },
  ];

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <div className="bg-white shadow-sm border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="py-4">
            <h1 className="text-2xl font-bold text-gray-900 mb-4">
              Advanced Search
            </h1>
            
            {/* Search Bar */}
            <div className="relative">
              <div className="flex items-center space-x-4">
                <div className="flex-1 relative">
                  <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" />
                  <input
                    type="text"
                    value={searchQuery}
                    onChange={(e) => setSearchQuery(e.target.value)}
                    onKeyPress={(e) => e.key === 'Enter' && handleSearch()}
                    placeholder="Cari izin usaha, registrasi, atau dokumen..."
                    className="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                  {suggestionsLoading && (
                    <Loader2 className="absolute right-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 animate-spin" />
                  )}
                </div>
                <button
                  onClick={() => handleSearch()}
                  disabled={loading}
                  className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
                >
                  {loading ? (
                    <Loader2 className="h-5 w-5 animate-spin" />
                  ) : (
                    <Search className="h-5 w-5" />
                  )}
                  <span>Cari</span>
                </button>
                <button
                  onClick={() => setShowFilters(!showFilters)}
                  className="px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-50 flex items-center space-x-2"
                >
                  <SlidersHorizontal className="h-5 w-5" />
                  <span>Filter</span>
                  {hasActiveFilters && (
                    <span className="bg-blue-600 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center">
                      {activeFilterCount}
                    </span>
                  )}
                </button>
              </div>

              {/* Search Suggestions */}
              {suggestions.length > 0 && searchQuery && (
                <div className="absolute top-full left-0 right-0 bg-white border border-gray-200 rounded-lg shadow-lg mt-1 z-10">
                  {suggestions.map((suggestion, index) => (
                    <button
                      key={index}
                      onClick={() => {
                        setSearchQuery(suggestion);
                        handleSearch(suggestion);
                      }}
                      className="w-full px-4 py-2 text-left hover:bg-gray-50 flex items-center space-x-2"
                    >
                      <Search className="h-4 w-4 text-gray-400" />
                      <span>{suggestion}</span>
                    </button>
                  ))}
                </div>
              )}
            </div>

            {/* Quick Filters */}
            {showFilters && (
              <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                <div className="flex items-center justify-between mb-4">
                  <h3 className="font-medium">Filter Pencarian</h3>
                  {hasActiveFilters && (
                    <button
                      onClick={clearAllFilters}
                      className="text-sm text-blue-600 hover:text-blue-700"
                    >
                      Hapus Semua Filter
                    </button>
                  )}
                </div>
                
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                  {/* Business Type Filter */}
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Jenis Usaha
                    </label>
                    <div className="space-y-2">
                      {['license', 'business', 'application'].map((type) => (
                        <label key={type} className="flex items-center">
                          <input
                            type="checkbox"
                            checked={isFilterActive('businessType', type)}
                            onChange={() => handleFilterChange('businessType', type)}
                            className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                          />
                          <span className="ml-2 text-sm">
                            {type === 'license' ? 'Izin Usaha' : 
                             type === 'business' ? 'Registrasi Bisnis' : 'Aplikasi'}
                          </span>
                        </label>
                      ))}
                    </div>
                  </div>

                  {/* License Status Filter */}
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Status Izin
                    </label>
                    <div className="space-y-2">
                      {['active', 'pending', 'expired'].map((status) => (
                        <label key={status} className="flex items-center">
                          <input
                            type="checkbox"
                            checked={isFilterActive('licenseStatus', status)}
                            onChange={() => handleFilterChange('licenseStatus', status)}
                            className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                          />
                          <span className="ml-2 text-sm">
                            {status === 'active' ? 'Aktif' : 
                             status === 'pending' ? 'Menunggu' : 'Kadaluarsa'}
                          </span>
                        </label>
                      ))}
                    </div>
                  </div>

                  {/* Business Size Filter */}
                  <div>
                    <label className="block text-sm font-medium text-gray-700 mb-2">
                      Ukuran Usaha
                    </label>
                    <select
                      value={filters.businessSize || ''}
                      onChange={(e) => handleFilterChange('businessSize', e.target.value)}
                      className="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                      <option value="">Semua Ukuran</option>
                      <option value="micro">Mikro</option>
                      <option value="small">Kecil</option>
                      <option value="medium">Menengah</option>
                    </select>
                  </div>
                </div>
              </div>
            )}
          </div>

          {/* Navigation Tabs */}
          <div className="flex space-x-8 overflow-x-auto">
            {tabs.map((tab) => {
              const Icon = tab.icon;
              return (
                <button
                  key={tab.key}
                  onClick={() => setActiveTab(tab.key as 'search' | 'results' | 'filters' | 'analytics')}
                  className={`flex items-center space-x-2 py-4 px-2 border-b-2 font-medium text-sm whitespace-nowrap ${
                    activeTab === tab.key
                      ? 'border-blue-500 text-blue-600'
                      : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
                  }`}
                >
                  <Icon className="h-5 w-5" />
                  <span>{tab.label}</span>
                </button>
              );
            })}
          </div>
        </div>
      </div>

      {/* Content */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {activeTab === 'search' && (
          <div className="space-y-8">
            {/* Popular Searches */}
            <div className="bg-white rounded-lg shadow p-6">
              <h2 className="text-lg font-semibold mb-4 flex items-center">
                <TrendingUp className="h-5 w-5 mr-2" />
                Pencarian Popular
              </h2>
              <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                {popularSearches.map((search, index) => (
                  <button
                    key={index}
                    onClick={() => handleSearch(search.term)}
                    className="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50"
                  >
                    <div className="flex items-center space-x-3">
                      <span className="text-sm font-medium">{search.term}</span>
                      <span className="text-xs text-gray-500">({search.count.toLocaleString()})</span>
                    </div>
                    <div className={`text-xs px-2 py-1 rounded ${
                      search.trend === 'up' ? 'bg-green-100 text-green-700' :
                      search.trend === 'down' ? 'bg-red-100 text-red-700' :
                      'bg-gray-100 text-gray-700'
                    }`}>
                      {search.trend === 'up' ? '↗' : search.trend === 'down' ? '↘' : '→'}
                    </div>
                  </button>
                ))}
              </div>
            </div>

            {/* Search History */}
            {history.length > 0 && (
              <div className="bg-white rounded-lg shadow p-6">
                <div className="flex items-center justify-between mb-4">
                  <h2 className="text-lg font-semibold flex items-center">
                    <Clock className="h-5 w-5 mr-2" />
                    Riwayat Pencarian
                  </h2>
                  <button
                    onClick={clearHistory}
                    className="text-sm text-red-600 hover:text-red-700"
                  >
                    Hapus Riwayat
                  </button>
                </div>
                <div className="space-y-2">
                  {history.map((item, index) => (
                    <div key={index} className="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                      <button
                        onClick={() => handleSearch(item)}
                        className="flex-1 text-left"
                      >
                        {item}
                      </button>
                      <button
                        onClick={() => removeFromHistory(item)}
                        className="text-gray-400 hover:text-gray-600"
                      >
                        <X className="h-4 w-4" />
                      </button>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        )}

        {activeTab === 'results' && (
          <div className="space-y-6">
            {/* Results Header */}
            {query && (
              <div className="bg-white rounded-lg shadow p-6">
                <div className="flex items-center justify-between mb-4">
                  <div>
                    <h2 className="text-lg font-semibold">
                      Hasil pencarian untuk: &quot;{query}&quot;
                    </h2>
                    <p className="text-sm text-gray-600">
                      {pagination.total.toLocaleString()} hasil ditemukan dalam {processingTime.toFixed(0)}ms
                    </p>
                  </div>
                  <div className="flex items-center space-x-2">
                    <ArrowUpDown className="h-4 w-4 text-gray-400" />
                    <select className="border border-gray-300 rounded px-3 py-1 text-sm">
                      <option>Relevansi</option>
                      <option>Tanggal</option>
                      <option>Judul</option>
                    </select>
                  </div>
                </div>
              </div>
            )}

            {/* Search Results */}
            {loading && (
              <div className="flex items-center justify-center py-12">
                <Loader2 className="h-8 w-8 animate-spin text-blue-600" />
              </div>
            )}

            {error && (
              <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                <p className="text-red-600">{error}</p>
              </div>
            )}

            {isEmpty && (
              <div className="bg-white rounded-lg shadow p-12 text-center">
                <Search className="h-12 w-12 text-gray-400 mx-auto mb-4" />
                <h3 className="text-lg font-medium text-gray-900 mb-2">
                  Tidak ada hasil ditemukan
                </h3>
                <p className="text-gray-600">
                  Coba ubah kata kunci atau filter pencarian Anda
                </p>
              </div>
            )}

            {hasResults && (
              <div className="space-y-4">
                {results.map((result) => (
                  <div key={result.id} className="bg-white rounded-lg shadow hover:shadow-md transition-shadow">
                    <div className="p-6">
                      <div className="flex items-start justify-between">
                        <div className="flex-1">
                          <div className="flex items-center space-x-2 mb-2">
                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                              result.type === 'license' ? 'bg-blue-100 text-blue-800' :
                              result.type === 'business' ? 'bg-green-100 text-green-800' :
                              result.type === 'application' ? 'bg-yellow-100 text-yellow-800' :
                              'bg-gray-100 text-gray-800'
                            }`}>
                              {result.type === 'license' ? 'Izin' :
                               result.type === 'business' ? 'Bisnis' :
                               result.type === 'application' ? 'Aplikasi' : 'Dokumen'}
                            </span>
                            <span className="text-sm text-gray-500">
                              Score: {(result.score * 100).toFixed(0)}%
                            </span>
                          </div>
                          
                          <h3 className="text-lg font-semibold text-gray-900 mb-2">
                            <span dangerouslySetInnerHTML={{ 
                              __html: result.highlight?.title?.[0] || result.title 
                            }} />
                          </h3>
                          
                          <p className="text-gray-600 mb-4">
                            <span dangerouslySetInnerHTML={{ 
                              __html: result.highlight?.description?.[0] || result.description 
                            }} />
                          </p>
                          
                          <div className="flex items-center space-x-4 text-sm text-gray-500">
                            <span>
                              Diperbarui: {new Date(result.lastModified).toLocaleDateString('id-ID')}
                            </span>
                            {result.metadata && typeof result.metadata === 'object' && (
                              <>
                                {'category' in result.metadata && result.metadata.category && (
                                  <span>• {String(result.metadata.category)}</span>
                                )}
                                {'difficulty' in result.metadata && result.metadata.difficulty && (
                                  <span>• {String(result.metadata.difficulty)}</span>
                                )}
                              </>
                            )}
                          </div>
                        </div>
                        
                        <button className="ml-4 p-2 text-gray-400 hover:text-yellow-500">
                          <Star className="h-5 w-5" />
                        </button>
                      </div>
                    </div>
                  </div>
                ))}

                {/* Load More */}
                {hasMore && (
                  <div className="text-center">
                    <button
                      onClick={loadMore}
                      disabled={loading}
                      className="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                    >
                      {loading ? 'Loading...' : 'Muat Lebih Banyak'}
                    </button>
                  </div>
                )}
              </div>
            )}
          </div>
        )}

        {activeTab === 'filters' && (
          <div className="bg-white rounded-lg shadow p-6">
            <h2 className="text-lg font-semibold mb-6">Filter & Facets</h2>
            
            {facets.length > 0 ? (
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {facets.map((facet) => (
                  <div key={facet.field} className="border border-gray-200 rounded-lg p-4">
                    <h3 className="font-medium mb-3">{facet.label}</h3>
                    <div className="space-y-2">
                      {facet.values.map((value) => (
                        <label key={value.value} className="flex items-center justify-between">
                          <div className="flex items-center">
                            <input
                              type="checkbox"
                              checked={isFilterActive(facet.field as keyof SearchFilters, value.value)}
                              onChange={() => handleFilterChange(facet.field as keyof SearchFilters, value.value)}
                              className="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            <span className="ml-2 text-sm">{value.label}</span>
                          </div>
                          <span className="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                            {value.count}
                          </span>
                        </label>
                      ))}
                    </div>
                  </div>
                ))}
              </div>
            ) : (
              <p className="text-gray-600 text-center py-8">
                Lakukan pencarian untuk melihat filter yang tersedia
              </p>
            )}
          </div>
        )}

        {activeTab === 'analytics' && (
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Search Performance */}
            <div className="bg-white rounded-lg shadow p-6">
              <h3 className="text-lg font-semibold mb-4">Performa Pencarian</h3>
              <div className="space-y-4">
                <div className="flex justify-between">
                  <span>Total Pencarian:</span>
                  <span className="font-medium">1,234</span>
                </div>
                <div className="flex justify-between">
                  <span>Rata-rata Waktu:</span>
                  <span className="font-medium">{processingTime.toFixed(0)}ms</span>
                </div>
                <div className="flex justify-between">
                  <span>Tingkat Keberhasilan:</span>
                  <span className="font-medium text-green-600">94.2%</span>
                </div>
              </div>
            </div>

            {/* Popular Categories */}
            <div className="bg-white rounded-lg shadow p-6">
              <h3 className="text-lg font-semibold mb-4">Kategori Popular</h3>
              <div className="space-y-3">
                {[
                  { name: 'Izin Usaha', count: 456, color: 'bg-blue-500' },
                  { name: 'NIB Registration', count: 324, color: 'bg-green-500' },
                  { name: 'SIUP Online', count: 234, color: 'bg-yellow-500' },
                  { name: 'CV Registration', count: 156, color: 'bg-purple-500' },
                ].map((category) => (
                  <div key={category.name} className="flex items-center space-x-3">
                    <div className={`w-3 h-3 rounded-full ${category.color}`} />
                    <span className="flex-1">{category.name}</span>
                    <span className="text-sm text-gray-500">{category.count}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
