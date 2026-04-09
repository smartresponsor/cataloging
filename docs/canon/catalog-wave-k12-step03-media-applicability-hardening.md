# Catalog Wave K12 Step03 — Media Applicability Hardening

## Summary

This wave adds channel-scoped and locale-scoped media applicability evaluation without introducing parallel architectural trees.

## Canon alignment

- stays inside `src/` with `App\ -> src/`
- uses canonical layer roots and matching interface roots
- keeps Symfony-oriented service / policy / event / value-object layout
- introduces no `Port`, `Adaptor`, `Infra`, `Catalog`, or `Cataloging` wrapper trees

## Delivered backend capability

- evaluation of governed bindings for a concrete `channel + locale` publication context
- required-role coverage checks for scoped publication targets
- normalized event payload for future readiness bridges
