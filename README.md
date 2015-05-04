Temp_Session
============

A extension of the CodeIgniter 1.x session library to support browser-session-expiring userdata.
Before this change, userdata wasmlong-lived, and tied to the user's persistant CI cookie. After this change, 
CI will also support temporary userdata that is tied to a session-expiring cookie.
