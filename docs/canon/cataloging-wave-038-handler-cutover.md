cataloging wave 038

Cut over controller-folder runtime classes from legacy `App\Http\Category` and `SmartResponsor\Category\Http` namespaces to canonical `App\Controller\Category`.

Compatibility policy:
- keep old FQCNs alive through `class_alias`
- keep file paths unchanged
- postpone physical file moves
