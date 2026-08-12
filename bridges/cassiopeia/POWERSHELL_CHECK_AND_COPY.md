# PowerShell — safe conflict check and copy

Assumptions:

    Joomla root:
    C:\wamp2\www\Joomla6T2

    Extracted bridge:
    C:\TEMP\jem_presentation_cassiopeia_thin_bridge_v0.1.0

## 1. Check for conflicts

```powershell
$joomla = "C:\wamp2\www\Joomla6T2"

$targets = @(
    "$joomla\templates\cassiopeia\html\com_jem\event\default.php",
    "$joomla\templates\cassiopeia\html\com_jem\event\responsive\default.php"
)

$targets | ForEach-Object {
    if (Test-Path $_) {
        Write-Host "EXISTS - DO NOT OVERWRITE: $_" -ForegroundColor Yellow
    } else {
        Write-Host "FREE: $_" -ForegroundColor Green
    }
}
```

Continue only when both paths are FREE, unless an existing override has first
been manually reviewed and deliberately integrated.

## 2. Copy

```powershell
$joomla = "C:\wamp2\www\Joomla6T2"
$source = "C:\TEMP\jem_presentation_cassiopeia_thin_bridge_v0.1.0"

New-Item -ItemType Directory -Force `
  "$joomla\templates\cassiopeia\html\com_jem\event\responsive" | Out-Null

Copy-Item `
  "$source\templates\cassiopeia\html\com_jem\event\default.php" `
  "$joomla\templates\cassiopeia\html\com_jem\event\default.php"

Copy-Item `
  "$source\templates\cassiopeia\html\com_jem\event\responsive\default.php" `
  "$joomla\templates\cassiopeia\html\com_jem\event\responsive\default.php"
```

## 3. Rollback

```powershell
$joomla = "C:\wamp2\www\Joomla6T2"

Remove-Item `
  "$joomla\templates\cassiopeia\html\com_jem\event\default.php" `
  -ErrorAction SilentlyContinue

Remove-Item `
  "$joomla\templates\cassiopeia\html\com_jem\event\responsive\default.php" `
  -ErrorAction SilentlyContinue
```
