cataloging wave 040

Add namespace gates for controller-folder runtime code.

Fail conditions:
- `src/Controller/Category/*` uses anything outside `App\Controller\Category*`
- any file under `src/` still declares legacy `App\Http\Category` or `SmartResponsor\Category\*` namespaces
