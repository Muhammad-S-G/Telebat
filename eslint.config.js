import prettier from 'eslint-config-prettier';
import vue from 'eslint-plugin-vue';

import {
  defineConfigWithVueTs,
  vueTsConfigs,
} from '@vue/eslint-config-typescript';

export default defineConfigWithVueTs(
  vue.configs['flat/essential'],
  vueTsConfigs.recommended,
  {
    ignores: [
      'vendor',
      'node_modules',
      'public',
      'bootstrap/ssr',
      'tailwind.config.js',
      'resources/js/components/ui/*',
    ],
  },
  {
    rules: {
      'vue/multi-word-component-names': 'off',
      '@typescript-eslint/no-explicit-any': 'off',
      'prettier/prettier': 'error',
      'node/no-unsupported-features/es-syntax': [
        'error',
        { version: '>=14.0.0', ignores: [] },
      ],
      'spaced-comment': 'off',
      'object-shorthand': 'off',
      'no-param-reassign': 'off',
      'class-methods-use-this': 'off',
      'no-console': 'off',
      'no-const-assign': 'error',
      'no-undef': 'off',
      'no-underscore-dangle': 'off',
      'no-process-exit': 'off',
      'func-names': 'off',
      'consistent-return': 'off',
      'no-return-await': 'off',
      'prefer-destructuring': ['error', { object: true, array: false }],
      'no-unused-vars': [
        'error',
        {
          argsIgnorePattern: 'req|res|next|val',
        },
      ],
    },
  },
  prettier,
);
