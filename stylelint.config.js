export default {
  extends: ['stylelint-config-standard'],
  ignoreFiles: ['node_modules/**', 'artifacts/**', 'betheme-audit/**'],
  rules: {
    'alpha-value-notation': null,
    'at-rule-empty-line-before': null,
    'color-function-alias-notation': null,
    'color-function-notation': null,
    'custom-property-pattern': null,
    'declaration-block-single-line-max-declarations': null,
    'media-feature-range-notation': null,
    'no-descending-specificity': null,
    'property-no-deprecated': null,
    'selector-attribute-quotes': null,
    'selector-class-pattern': null,
    'selector-pseudo-element-colon-notation': null,
    'value-keyword-case': null
  }
};
