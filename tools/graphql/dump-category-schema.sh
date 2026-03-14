#!/bin/sh
set -e
OUT=report/catalog-graphql-schema-v1.graphql
cat > $OUT <<'GQL'
type Category {
  id: ID!
  name: String!
  slug: String!
  locale: String
  published: Boolean
  channel: String
}
type Query {
  categories(locale: String, channel: String): [Category!]!
}
GQL
echo '{"current":"v1"}' > report/catalog-graphql-schema-index.json
