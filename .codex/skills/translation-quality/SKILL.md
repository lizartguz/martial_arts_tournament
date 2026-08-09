---
name: translation-quality
description: Validate and improve multilingual UI text and Laravel translation files. Use when editing or reviewing `resources/lang/**`, JSON translation files, language selector labels, menu labels, Blade text using `__()`, or any task involving Spanish, English, Portuguese, French, German, Russian, accents, Unicode, or translation quality.
---

# Translation Quality

## Workflow

1. Before editing translations, identify every source of text involved:
   - Laravel PHP language files: `resources/lang/{locale}/*.php`
   - Laravel JSON translations: `resources/lang/{locale}.json`
   - Blade literals using `__('...')`
   - Language labels in components such as language selectors

2. Preserve native spelling and characters:
   - Spanish: use `Español`, `Gráficos`, `Contraseña`, `Cerrar sesión`.
   - Portuguese: use `Português`, `Configurações`, `Sessões`.
   - French: use `Français`, `Déconnexion`, `Paramètres`.
   - German: use `Deutsch`, `Löschen`, `Bestätigung`.
   - Russian: use real Cyrillic, for example `Русский`, `Профиль`, `Выйти`.

3. Write files as UTF-8. Avoid PowerShell text paths that can replace Unicode with `?`.
   Prefer `apply_patch` for small edits. For generated translation updates, use a UTF-8-safe script and validate afterward.

4. After editing, validate:
   - JSON files parse with `json.loads`.
   - PHP translation files pass `php -l`.
   - No decoded translation file contains corruption markers like `�`, `Ã`, `Â`, `Ð`, `Ñ`, `Espa?`, `Fran?`, `Portugu?`, or `????`.
   - Normal question marks in real questions are acceptable.

5. For Laravel views, check coverage for direct translation keys:
   - Search Blade files for `__('...')`.
   - Add missing keys to every active locale when the text is user-facing.
   - Clear cached views/config if needed with `php artisan view:clear` and `php artisan config:clear`.

## Validation Snippets

Use Python to verify JSON translation files without trusting terminal display encoding:

```powershell
@'
import json
from pathlib import Path

bad_chars = [chr(0xfffd), chr(0x00c3), chr(0x00c2), chr(0x00d0), chr(0x00d1)]
bad_sequences = ['Espa?', 'Fran?', 'Portugu?', '????']

for path in sorted(Path('resources/lang').glob('*.json')):
    text = path.read_text(encoding='utf-8')
    json.loads(text)
    found_chars = [(hex(ord(ch)), text.count(ch)) for ch in bad_chars if ch in text]
    found_seq = [seq for seq in bad_sequences if seq in text]
    print(path.name, 'bad_chars=', found_chars, 'bad_sequences=', found_seq)
'@ | python -
```

Use escaped output to verify exact characters when the console looks suspicious:

```powershell
@'
import json
from pathlib import Path

for locale in ['es', 'fr', 'de', 'pt', 'ru']:
    path = Path(f'resources/lang/{locale}.json')
    if not path.exists():
        continue
    data = json.loads(path.read_text(encoding='utf-8'))
    print(locale, data.get('Log Out', '').encode('unicode_escape').decode('ascii'))
'@ | python -
```

## Reporting

When finishing translation work, mention:

- Which language files changed.
- Whether UTF-8/corruption validation passed.
- Whether translation key coverage was checked.
- Any cache or test command that was run.
