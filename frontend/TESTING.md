# Testing Guide

## Production Testing

All production tests are located in the `tests/` directory.

### Running Tests

```bash
# Run all tests
npm test

# Run specific test file
npm test tests/production.test.ts

# Run tests with coverage
npm run test:coverage

# Run tests in watch mode
npm run test:watch
```

### Test Files

- `tests/setup.ts` - Global test setup and mocks (TypeScript)
- `tests/production.test.ts` - Production readiness tests (TypeScript)
- `tests/basic.test.js` - Basic Jest functionality test (JavaScript)

### Recent Fixes

✅ **Resolved Issues:**
- Fixed `whatwg-fetch` module not found error
- Replaced external fetch polyfill with Jest mock
- All TypeScript/ESLint errors resolved
- Proper TypeScript support with ts-jest

### Test Configuration

Jest is configured with:
- **ts-jest preset** for TypeScript support
- **jsdom environment** for DOM testing
- **Global fetch mock** (no external polyfill needed)
- **Automatic mocking** for Next.js components
- **Coverage collection** enabled
- **10 second timeout** for async tests

### Test Categories

1. **Authentication System** - Login, logout, error handling
2. **API Integration** - Government APIs, payment gateway
3. **Performance Tests** - Load times, data processing
4. **Security Tests** - Input sanitization, JWT validation
5. **Accessibility Tests** - ARIA labels, keyboard navigation
6. **PWA Functionality** - Service worker, offline mode
7. **Data Validation** - Email, phone number formats
8. **Error Handling** - Network failures, server errors
9. **Localization** - Indonesian currency, date formatting

All tests use TypeScript and include proper type checking to ensure production quality.
