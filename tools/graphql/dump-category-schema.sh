#!/bin/sh
set -e
OUT=report/category-graphql-schema-v1.graphql
cat > $OUT <<'GQL'
type tests {
  id: ID!
  name: String!
  slug: String!
  locale: String
  published: Boolean
  channel: String
}
type Query {
  categories(locale: String, channel: String): [tests!]!
}
GQL
echo '{"current":"v1"}' > report/category-graphql-schema-index.json
