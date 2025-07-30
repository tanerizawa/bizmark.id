/**
 * Advanced Search Service
 * Provides enhanced search capabilities with filters, facets, and intelligent ranking
 */

export interface SearchFilters {
  businessType?: string[];
  licenseStatus?: string[];
  dateRange?: {
    from: string;
    to: string;
  };
  location?: {
    province?: string;
    city?: string;
    district?: string;
  };
  businessSize?: 'micro' | 'small' | 'medium';
  tags?: string[];
}

export interface SearchFacet {
  field: string;
  label: string;
  values: Array<{
    value: string;
    label: string;
    count: number;
  }>;
}

export interface SearchResult<T = unknown> {
  id: string;
  title: string;
  description: string;
  type: 'business' | 'license' | 'application' | 'document';
  score: number;
  highlight?: {
    title?: string[];
    description?: string[];
    content?: string[];
  };
  metadata: T;
  url: string;
  lastModified: string;
}

export interface SearchResponse<T = unknown> {
  query: string;
  filters: SearchFilters;
  results: SearchResult<T>[];
  facets: SearchFacet[];
  pagination: {
    page: number;
    pageSize: number;
    total: number;
    totalPages: number;
  };
  suggestions?: string[];
  processingTime: number;
  timestamp: string;
}

export interface SearchOptions {
  page?: number;
  pageSize?: number;
  sortBy?: 'relevance' | 'date' | 'title' | 'status';
  sortOrder?: 'asc' | 'desc';
  enableHighlight?: boolean;
  enableFacets?: boolean;
  enableSuggestions?: boolean;
}

class AdvancedSearchService {
  private baseUrl: string;
  private apiKey: string;

  constructor(baseUrl: string, apiKey: string) {
    this.baseUrl = baseUrl;
    this.apiKey = apiKey;
  }

  /**
   * Perform advanced search with filters and facets
   */
  async search<T = unknown>(
    query: string,
    filters: SearchFilters = {},
    options: SearchOptions = {}
  ): Promise<SearchResponse<T>> {
    const {
      page = 1,
      pageSize = 20,
      sortBy = 'relevance',
      sortOrder = 'desc',
      enableHighlight = true,
      enableFacets = true,
      enableSuggestions = true,
    } = options;

    try {
      const searchPayload = {
        query: query.trim(),
        filters,
        page,
        pageSize,
        sort: {
          field: sortBy,
          order: sortOrder,
        },
        highlight: enableHighlight,
        facets: enableFacets,
        suggestions: enableSuggestions,
      };

      const response = await fetch(`${this.baseUrl}/search`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${this.apiKey}`,
        },
        body: JSON.stringify(searchPayload),
      });

      if (!response.ok) {
        throw new Error(`Search failed: ${response.statusText}`);
      }

      const data = await response.json();

      return {
        query,
        filters,
        results: data.hits?.hits?.map((hit: {
          _id: string;
          _score: number;
          _source: {
            title: string;
            description: string;
            type: string;
            metadata?: T;
            url?: string;
            lastModified?: string;
          };
          highlight?: {
            title?: string[];
            description?: string[];
            content?: string[];
          };
        }) => ({
          id: hit._id,
          title: hit._source.title,
          description: hit._source.description,
          type: hit._source.type,
          score: hit._score,
          highlight: hit.highlight,
          metadata: hit._source.metadata || {},
          url: hit._source.url || '#',
          lastModified: hit._source.lastModified || new Date().toISOString(),
        })) || [],
        facets: this.parseFacets(data.aggregations),
        pagination: {
          page,
          pageSize,
          total: data.hits?.total?.value || 0,
          totalPages: Math.ceil((data.hits?.total?.value || 0) / pageSize),
        },
        suggestions: data.suggest?.suggestions?.map((s: { text: string }) => s.text) || [],
        processingTime: data.took || 0,
        timestamp: new Date().toISOString(),
      };
    } catch {
      // Fallback to mock data for development
      return this.getMockSearchResults(query, filters, options);
    }
  }

  /**
   * Get search suggestions based on partial query
   */
  async getSuggestions(partialQuery: string, limit: number = 10): Promise<string[]> {
    if (!partialQuery.trim() || partialQuery.length < 2) {
      return [];
    }

    try {
      const response = await fetch(`${this.baseUrl}/suggest`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${this.apiKey}`,
        },
        body: JSON.stringify({
          query: partialQuery,
          limit,
        }),
      });

      if (!response.ok) {
        throw new Error(`Suggestions failed: ${response.statusText}`);
      }

      const data = await response.json();
      return data.suggestions || [];
    } catch {
      // Fallback suggestions
      return this.getMockSuggestions(partialQuery).slice(0, limit);
    }
  }

  /**
   * Get popular search terms
   */
  async getPopularSearches(limit: number = 10): Promise<Array<{
    term: string;
    count: number;
    trend: 'up' | 'down' | 'stable';
  }>> {
    try {
      const response = await fetch(`${this.baseUrl}/popular-searches?limit=${limit}`, {
        headers: {
          'Authorization': `Bearer ${this.apiKey}`,
        },
      });

      if (!response.ok) {
        throw new Error(`Popular searches failed: ${response.statusText}`);
      }

      const data = await response.json();
      return data.popular || [];
    } catch {
      // Fallback popular searches
      return [
        { term: 'izin usaha', count: 1250, trend: 'up' as const },
        { term: 'NIB online', count: 980, trend: 'up' as const },
        { term: 'siup online', count: 756, trend: 'stable' as const },
        { term: 'cv registration', count: 645, trend: 'down' as const },
        { term: 'pt registration', count: 543, trend: 'stable' as const },
        { term: 'umkm license', count: 432, trend: 'up' as const },
        { term: 'oss registration', count: 321, trend: 'up' as const },
        { term: 'business permit', count: 298, trend: 'stable' as const },
      ].slice(0, limit);
    }
  }

  /**
   * Save search query for analytics
   */
  async saveSearchQuery(query: string, filters: SearchFilters, resultCount: number): Promise<void> {
    try {
      await fetch(`${this.baseUrl}/analytics/search`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${this.apiKey}`,
        },
        body: JSON.stringify({
          query,
          filters,
          resultCount,
          timestamp: new Date().toISOString(),
        }),
      });
    } catch (error) {
      console.log('Search analytics logging failed:', error);
    }
  }

  /**
   * Parse Elasticsearch facets/aggregations
   */
  private parseFacets(aggregations: Record<string, {
    buckets?: Array<{ key: string; doc_count: number }>;
  }>): SearchFacet[] {
    if (!aggregations) return [];

    const facets: SearchFacet[] = [];

    Object.keys(aggregations).forEach(key => {
      const agg = aggregations[key];
      if (agg.buckets) {
        facets.push({
          field: key,
          label: this.getFacetLabel(key),
          values: agg.buckets.map((bucket) => ({
            value: bucket.key,
            label: bucket.key,
            count: bucket.doc_count,
          })),
        });
      }
    });

    return facets;
  }

  /**
   * Get human-readable facet labels
   */
  private getFacetLabel(field: string): string {
    const labels: Record<string, string> = {
      businessType: 'Jenis Usaha',
      licenseStatus: 'Status Izin',
      location: 'Lokasi',
      businessSize: 'Ukuran Usaha',
      tags: 'Kategori',
    };
    return labels[field] || field;
  }

  /**
   * Mock search results for development
   */
  private getMockSearchResults<T>(
    query: string,
    filters: SearchFilters,
    options: SearchOptions
  ): SearchResponse<T> {
    const mockResults: SearchResult[] = [
      {
        id: '1',
        title: 'Izin Usaha Mikro Kecil (IUMK)',
        description: 'Panduan lengkap untuk mengurus Izin Usaha Mikro Kecil (IUMK) secara online melalui OSS.',
        type: 'license',
        score: 0.95,
        highlight: {
          title: ['<em>Izin Usaha</em> Mikro Kecil (IUMK)'],
          description: ['Panduan lengkap untuk mengurus <em>Izin Usaha</em> Mikro Kecil'],
        },
        metadata: {
          category: 'Izin Usaha',
          difficulty: 'Mudah',
          estimatedTime: '1-3 hari',
        },
        url: '/licenses/iumk',
        lastModified: '2025-07-30T10:00:00Z',
      },
      {
        id: '2',
        title: 'NIB (Nomor Induk Berusaha) Online',
        description: 'Cara mendapatkan NIB secara online melalui sistem OSS untuk semua jenis usaha.',
        type: 'business',
        score: 0.89,
        highlight: {
          title: ['<em>NIB</em> (Nomor Induk Berusaha) Online'],
          description: ['Cara mendapatkan <em>NIB</em> secara online melalui sistem OSS'],
        },
        metadata: {
          category: 'Registrasi Bisnis',
          difficulty: 'Sedang',
          estimatedTime: '2-5 hari',
        },
        url: '/businesses/nib-registration',
        lastModified: '2025-07-29T15:30:00Z',
      },
      {
        id: '3',
        title: 'CV Registration Process',
        description: 'Step-by-step guide untuk mendaftarkan CV (Commanditaire Vennootschap) dan persyaratannya.',
        type: 'business',
        score: 0.76,
        highlight: {
          title: ['<em>CV Registration</em> Process'],
          description: ['Step-by-step guide untuk mendaftarkan <em>CV</em>'],
        },
        metadata: {
          category: 'Badan Hukum',
          difficulty: 'Sulit',
          estimatedTime: '7-14 hari',
        },
        url: '/businesses/cv-registration',
        lastModified: '2025-07-28T09:15:00Z',
      },
    ];

    const filteredResults = mockResults.filter(result => {
      if (query.trim()) {
        const searchTerm = query.toLowerCase();
        return result.title.toLowerCase().includes(searchTerm) ||
               result.description.toLowerCase().includes(searchTerm);
      }
      return true;
    });

    return {
      query,
      filters,
      results: filteredResults as SearchResult<T>[],
      facets: [
        {
          field: 'businessType',
          label: 'Jenis Usaha',
          values: [
            { value: 'license', label: 'Izin Usaha', count: 145 },
            { value: 'business', label: 'Registrasi Bisnis', count: 89 },
            { value: 'application', label: 'Aplikasi', count: 67 },
          ],
        },
        {
          field: 'licenseStatus',
          label: 'Status Izin',
          values: [
            { value: 'active', label: 'Aktif', count: 234 },
            { value: 'pending', label: 'Menunggu', count: 45 },
            { value: 'expired', label: 'Kadaluarsa', count: 23 },
          ],
        },
      ],
      pagination: {
        page: options.page || 1,
        pageSize: options.pageSize || 20,
        total: filteredResults.length,
        totalPages: Math.ceil(filteredResults.length / (options.pageSize || 20)),
      },
      suggestions: this.getMockSuggestions(query),
      processingTime: Math.random() * 100 + 50,
      timestamp: new Date().toISOString(),
    };
  }

  /**
   * Mock suggestions for development
   */
  private getMockSuggestions(query: string): string[] {
    const allSuggestions = [
      'izin usaha mikro',
      'izin usaha kecil',
      'izin usaha menengah',
      'nib online registration',
      'nib nomor induk berusaha',
      'siup online',
      'siup surat izin usaha perdagangan',
      'cv registration',
      'cv commanditaire vennootschap',
      'pt registration',
      'pt perseroan terbatas',
      'umkm license',
      'umkm usaha mikro kecil menengah',
      'oss online single submission',
      'business permit indonesia',
    ];

    if (!query.trim()) return allSuggestions.slice(0, 8);

    return allSuggestions
      .filter(suggestion => suggestion.toLowerCase().includes(query.toLowerCase()))
      .slice(0, 8);
  }
}

// Create singleton instance
let advancedSearchInstance: AdvancedSearchService | null = null;

export const createAdvancedSearchService = (baseUrl: string, apiKey: string): AdvancedSearchService => {
  if (!advancedSearchInstance) {
    advancedSearchInstance = new AdvancedSearchService(baseUrl, apiKey);
  }
  return advancedSearchInstance;
};

export const getAdvancedSearchService = (): AdvancedSearchService => {
  if (!advancedSearchInstance) {
    // Fallback configuration for development
    advancedSearchInstance = new AdvancedSearchService(
      process.env.NEXT_PUBLIC_SEARCH_API_URL || 'http://localhost:9200',
      process.env.NEXT_PUBLIC_SEARCH_API_KEY || 'development'
    );
  }
  return advancedSearchInstance;
};

export default AdvancedSearchService;
