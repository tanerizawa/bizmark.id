/**
 * Advanced Search Hooks
 * React hooks for enhanced search functionality with filters, facets, and intelligent ranking
 */

import { useState, useEffect, useCallback, useMemo } from 'react';
import { 
  getAdvancedSearchService,
  SearchFilters,
  SearchResult,
  SearchResponse,
  SearchOptions,
  SearchFacet
} from '@/services/advancedSearch';

// Search state interface
interface SearchState<T = unknown> {
  query: string;
  filters: SearchFilters;
  options: SearchOptions;
  results: SearchResult<T>[];
  facets: SearchFacet[];
  suggestions: string[];
  popularSearches: Array<{
    term: string;
    count: number;
    trend: 'up' | 'down' | 'stable';
  }>;
  loading: boolean;
  error: string | null;
  pagination: {
    page: number;
    pageSize: number;
    total: number;
    totalPages: number;
  };
  processingTime: number;
}

/**
 * Main search hook with comprehensive search functionality
 */
export const useAdvancedSearch = <T = unknown>(
  initialQuery: string = '',
  initialFilters: SearchFilters = {},
  initialOptions: SearchOptions = {}
) => {
  const [state, setState] = useState<SearchState<T>>({
    query: initialQuery,
    filters: initialFilters,
    options: {
      page: 1,
      pageSize: 20,
      sortBy: 'relevance',
      sortOrder: 'desc',
      enableHighlight: true,
      enableFacets: true,
      enableSuggestions: true,
      ...initialOptions,
    },
    results: [],
    facets: [],
    suggestions: [],
    popularSearches: [],
    loading: false,
    error: null,
    pagination: {
      page: 1,
      pageSize: 20,
      total: 0,
      totalPages: 0,
    },
    processingTime: 0,
  });

  const searchService = useMemo(() => getAdvancedSearchService(), []);

  // Perform search
  const search = useCallback(async (
    query?: string,
    filters?: SearchFilters,
    options?: SearchOptions
  ) => {
    const searchQuery = query ?? state.query;
    const searchFilters = filters ?? state.filters;
    const searchOptions = { ...state.options, ...options };

    if (!searchQuery.trim() && Object.keys(searchFilters).length === 0) {
      setState(prev => ({
        ...prev,
        results: [],
        pagination: { page: 1, pageSize: 20, total: 0, totalPages: 0 },
        processingTime: 0,
      }));
      return;
    }

    setState(prev => ({ ...prev, loading: true, error: null }));

    try {
      const response: SearchResponse<T> = await searchService.search(
        searchQuery,
        searchFilters,
        searchOptions
      );

      setState(prev => ({
        ...prev,
        query: searchQuery,
        filters: searchFilters,
        options: searchOptions,
        results: response.results,
        facets: response.facets,
        suggestions: response.suggestions || [],
        pagination: response.pagination,
        processingTime: response.processingTime,
        loading: false,
      }));

      // Save search for analytics
      await searchService.saveSearchQuery(
        searchQuery,
        searchFilters,
        response.results.length
      );
    } catch (error) {
      setState(prev => ({
        ...prev,
        loading: false,
        error: error instanceof Error ? error.message : 'Search failed',
      }));
    }
  }, [searchService, state.query, state.filters, state.options]);

  // Update query
  const setQuery = useCallback((query: string) => {
    setState(prev => ({ ...prev, query }));
  }, []);

  // Update filters
  const setFilters = useCallback((filters: SearchFilters) => {
    setState(prev => ({ ...prev, filters }));
  }, []);

  // Update options
  const setOptions = useCallback((options: SearchOptions) => {
    setState(prev => ({ ...prev, options: { ...prev.options, ...options } }));
  }, []);

  // Clear all filters
  const clearFilters = useCallback(() => {
    setState(prev => ({ ...prev, filters: {} }));
  }, []);

  // Clear search
  const clearSearch = useCallback(() => {
    setState(prev => ({
      ...prev,
      query: '',
      filters: {},
      results: [],
      suggestions: [],
      pagination: { page: 1, pageSize: 20, total: 0, totalPages: 0 },
      processingTime: 0,
    }));
  }, []);

  // Load more results (pagination)
  const loadMore = useCallback(async () => {
    if (state.pagination.page >= state.pagination.totalPages) return;

    const nextPage = state.pagination.page + 1;
    await search(state.query, state.filters, { ...state.options, page: nextPage });
  }, [search, state.query, state.filters, state.options, state.pagination]);

  // Auto-search when query, filters, or options change
  useEffect(() => {
    const timeoutId = setTimeout(() => {
      search();
    }, 300); // Debounce search

    return () => clearTimeout(timeoutId);
  }, [state.query, state.filters, state.options.sortBy, state.options.sortOrder]);

  return {
    // State
    query: state.query,
    filters: state.filters,
    options: state.options,
    results: state.results,
    facets: state.facets,
    suggestions: state.suggestions,
    loading: state.loading,
    error: state.error,
    pagination: state.pagination,
    processingTime: state.processingTime,

    // Actions
    search,
    setQuery,
    setFilters,
    setOptions,
    clearFilters,
    clearSearch,
    loadMore,

    // Computed values
    hasResults: state.results.length > 0,
    hasMore: state.pagination.page < state.pagination.totalPages,
    isEmpty: !state.loading && state.results.length === 0 && state.query.trim() !== '',
  };
};

/**
 * Hook for search suggestions with debounced input
 */
export const useSearchSuggestions = (
  query: string,
  delay: number = 300,
  limit: number = 10
) => {
  const [suggestions, setSuggestions] = useState<string[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const searchService = useMemo(() => getAdvancedSearchService(), []);

  useEffect(() => {
    if (!query.trim() || query.length < 2) {
      setSuggestions([]);
      return;
    }

    const timeoutId = setTimeout(async () => {
      setLoading(true);
      setError(null);

      try {
        const results = await searchService.getSuggestions(query, limit);
        setSuggestions(results);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to get suggestions');
        setSuggestions([]);
      } finally {
        setLoading(false);
      }
    }, delay);

    return () => clearTimeout(timeoutId);
  }, [query, delay, limit, searchService]);

  return { suggestions, loading, error };
};

/**
 * Hook for popular search terms
 */
export const usePopularSearches = (limit: number = 10) => {
  const [popularSearches, setPopularSearches] = useState<Array<{
    term: string;
    count: number;
    trend: 'up' | 'down' | 'stable';
  }>>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const searchService = useMemo(() => getAdvancedSearchService(), []);

  useEffect(() => {
    const fetchPopularSearches = async () => {
      setLoading(true);
      setError(null);

      try {
        const results = await searchService.getPopularSearches(limit);
        setPopularSearches(results);
      } catch (err) {
        setError(err instanceof Error ? err.message : 'Failed to get popular searches');
        setPopularSearches([]);
      } finally {
        setLoading(false);
      }
    };

    fetchPopularSearches();
  }, [limit, searchService]);

  return { popularSearches, loading, error };
};

/**
 * Hook for search filters management
 */
export const useSearchFilters = (initialFilters: SearchFilters = {}) => {
  const [filters, setFilters] = useState<SearchFilters>(initialFilters);

  // Add filter value
  const addFilter = useCallback((field: keyof SearchFilters, value: unknown) => {
    setFilters(prev => {
      const newFilters = { ...prev };
      
      if (field === 'businessType' || field === 'licenseStatus' || field === 'tags') {
        const currentValues = newFilters[field] || [];
        if (typeof value === 'string' && !currentValues.includes(value)) {
          newFilters[field] = [...currentValues, value];
        }
      } else {
        // Use type assertion for complex filter types
        (newFilters as Record<string, unknown>)[field] = value;
      }
      
      return newFilters;
    });
  }, []);

  // Remove filter value
  const removeFilter = useCallback((field: keyof SearchFilters, value?: string) => {
    setFilters(prev => {
      const newFilters = { ...prev };
      
      if (value && (field === 'businessType' || field === 'licenseStatus' || field === 'tags')) {
        const currentValues = newFilters[field] || [];
        newFilters[field] = currentValues.filter(v => v !== value);
        if (newFilters[field]?.length === 0) {
          delete newFilters[field];
        }
      } else {
        delete newFilters[field];
      }
      
      return newFilters;
    });
  }, []);

  // Clear all filters
  const clearAllFilters = useCallback(() => {
    setFilters({});
  }, []);

  // Toggle filter value (add if not present, remove if present)
  const toggleFilter = useCallback((field: keyof SearchFilters, value: string) => {
    setFilters(prev => {
      const newFilters = { ...prev };
      
      if (field === 'businessType' || field === 'licenseStatus' || field === 'tags') {
        const currentValues = newFilters[field] || [];
        if (currentValues.includes(value)) {
          newFilters[field] = currentValues.filter(v => v !== value);
          if (newFilters[field]?.length === 0) {
            delete newFilters[field];
          }
        } else {
          newFilters[field] = [...currentValues, value];
        }
      } else {
        if (newFilters[field] === value) {
          delete newFilters[field];
        } else {
          (newFilters as Record<string, unknown>)[field] = value;
        }
      }
      
      return newFilters;
    });
  }, []);

  // Check if filter is active
  const isFilterActive = useCallback((field: keyof SearchFilters, value?: string) => {
    if (!filters[field]) return false;
    
    if (value && Array.isArray(filters[field])) {
      return (filters[field] as string[]).includes(value);
    }
    
    return value ? filters[field] === value : Boolean(filters[field]);
  }, [filters]);

  // Get active filter count
  const activeFilterCount = useMemo(() => {
    let count = 0;
    Object.values(filters).forEach(value => {
      if (Array.isArray(value)) {
        count += value.length;
      } else if (value) {
        count += 1;
      }
    });
    return count;
  }, [filters]);

  return {
    filters,
    setFilters,
    addFilter,
    removeFilter,
    clearAllFilters,
    toggleFilter,
    isFilterActive,
    activeFilterCount,
    hasActiveFilters: activeFilterCount > 0,
  };
};

/**
 * Hook for search history management
 */
export const useSearchHistory = (maxItems: number = 10) => {
  const [history, setHistory] = useState<string[]>([]);

  // Load history from localStorage on mount
  useEffect(() => {
    const savedHistory = localStorage.getItem('searchHistory');
    if (savedHistory) {
      try {
        const parsed = JSON.parse(savedHistory);
        setHistory(Array.isArray(parsed) ? parsed.slice(0, maxItems) : []);
      } catch {
        setHistory([]);
      }
    }
  }, [maxItems]);

  // Add search to history
  const addToHistory = useCallback((query: string) => {
    if (!query.trim()) return;

    setHistory(prev => {
      const newHistory = [query, ...prev.filter(item => item !== query)].slice(0, maxItems);
      localStorage.setItem('searchHistory', JSON.stringify(newHistory));
      return newHistory;
    });
  }, [maxItems]);

  // Remove from history
  const removeFromHistory = useCallback((query: string) => {
    setHistory(prev => {
      const newHistory = prev.filter(item => item !== query);
      localStorage.setItem('searchHistory', JSON.stringify(newHistory));
      return newHistory;
    });
  }, []);

  // Clear history
  const clearHistory = useCallback(() => {
    setHistory([]);
    localStorage.removeItem('searchHistory');
  }, []);

  return {
    history,
    addToHistory,
    removeFromHistory,
    clearHistory,
    hasHistory: history.length > 0,
  };
};
