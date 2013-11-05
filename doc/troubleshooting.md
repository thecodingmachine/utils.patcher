Troubleshooting
===============

Apache stops and restarts when you apply a patch
------------------------------------------------

This can occur when you apply a database patch, mainly on Windows environments.
Apache comes with a small thread stack on some Windows install by default, and you should
increase its size.

Go to your **Apache 2** configuration file, and add those lines:

```
<IfModule mpm_winnt_module>
   ThreadStackSize 8388608
</IfModule>
```

This will increase the Apache stacktrace to 8Mo. You can learn more about it [on StackOverflow](http://stackoverflow.com/questions/5058845/how-do-i-increase-the-stack-size-for-apache-running-under-windows-7).
