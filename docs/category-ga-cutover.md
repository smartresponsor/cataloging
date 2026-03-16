# tests GA cutover

1. freeze rc1
2. run smoke v2
3. run canary window (48h)
4. if green → tag category-v1.0.0
5. if red → rollback to previous rc
