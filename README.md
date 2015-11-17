NHS Manager
===============

NHS Manager is an online platform adapted from existing Bootstrap themes. Its job is to be a databank that stores and manages our school's Chapter of the National Honor Society members.  Such data points include required service hours (either tutoring or community-based service), meeting minutes, and upcoming events.  Chapter Officers have unique privileges that allow them to manage different aspects of the society. Those users are granted special permission to use NHS Manager for more sensitive material.

Requires an SQL database for information storage.  The configured database has a three tables:
- "events" has columns: id, title, date, color, icon, description, and going
- "members" has columns: username, password, token, studentname, role, tutoring, community, approved, pending, lastlogin and logins
- "minutes" has columns: date, link, and absent
- "notification_email" has columns: username, recipient, eventReminder, newAnnouncement, newTutoring, newEvents, newMinutes, and newApproval
- "notification_phone" has columns: username, recipient, eventReminder, newAnnouncement, newTutoring, newEvents, newMinutes, and newApproval
- "tutor_req" has columns: id, name, grade, subjects, and free
- "vars" has columns: key and value

Special Permissions:
- President: can add events to timeline
- VP: can add events to timeline
- Secretary: can add events to timeline and can upload meeting minutes
- Treasurer: can add events to timeline
- Parliamentarian: can add events to timeline and can approve service hours (feature will be added)
- Historian: can add events to timeline
- Webmaster/Administrator: has access to all privileges
