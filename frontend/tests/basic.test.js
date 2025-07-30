/**
 * Simple test to verify Jest is working
 */

describe('Basic Test', () => {
  test('should pass basic test', () => {
    expect(1 + 1).toBe(2);
  });

  test('should have access to global variables', () => {
    expect(global).toBeDefined();
  });
});
