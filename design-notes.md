# Design
Notes, thoughts and decisions on how the BBS works from a technical and a user standpoint. 

## User

### Moderation
A moderator is the owner of a section and is held responsible for that section and the topics within it. 


They can do anything to topics, posts and sectons within the sections they moderate. 
They can add and delete topics and new sections to sections which they moderate.
They assign ownership of sub sections to other users. 
They can move sub sections to other sections which they moderate.
They can move topics to other sections which they moderate.
Thye can reorder topics within a section they moderate.
They can re-order child sections within a section they moderate.


Child Sections

Moderators can 
- create child sections
- update child sections
- appoint moderator to child sections
- move child sections
- delete child sections
- re-order child sections

Sections
- update section
    
Topics

Moderators can
- create topic
- update topic
- move topics to other moderated sections
- delete topic
- re-order topic


## Developer

### Authentication 
All authentication should be done within a laravel policy. Each model should have a policy. 
Authentication should not take place within the Request. 

Authentication should called up from the controller methods. 

### Validation 

Validation should take place within a FormRequest 