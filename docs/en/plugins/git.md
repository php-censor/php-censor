Plugin Git
==========

Runs git command for an specific branch. Supports `merge`, `tag`, `pull` and `push` commands

Configuration
-------------

### Examples

```yaml
complete:
    git:
        master:           <-- branch
            tag:          <-- action
                name: ""  <-- Action options
    
```

**Note:** Each action has its own options. 
