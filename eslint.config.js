export default [
  { ignores: ['node_modules/**', 'artifacts/**', 'betheme-audit/**'] },
  {
    files: ['scripts/**/*.{js,mjs}', 'services/**/*.js', 'packages/**/*.js'],
    languageOptions: {
      ecmaVersion: 'latest',
      sourceType: 'module',
      globals: {
        Buffer: 'readonly', URL: 'readonly', console: 'readonly', crypto: 'readonly', document: 'readonly', fetch: 'readonly',
        process: 'readonly', structuredClone: 'readonly', window: 'readonly', setTimeout: 'readonly'
      }
    },
    rules: {
      'eqeqeq': ['error', 'always'],
      'no-constant-condition': 'error',
      'no-debugger': 'error',
      'no-eval': 'error',
      'no-implied-eval': 'error',
      'no-new-func': 'error',
      'no-undef': 'error',
      'no-unused-vars': ['error', { argsIgnorePattern: '^_', caughtErrors: 'none' }],
      'prefer-const': 'error'
    }
  }
];
