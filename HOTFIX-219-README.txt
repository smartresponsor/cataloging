Hotfix 219

Purpose:
- remove hard dependency on GraphQL\Type\Definition\ResolveInfo in CategoryQuery/test
- allow current PHPUnit contour to run without requiring the GraphQL package class to exist

Files:
- src/GraphQl/CategoryQuery.php
- tests/GraphQl/CatalogQueryAdvancedTest.php
