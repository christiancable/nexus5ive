# modes

nexus has many nodes - only one node is the active mode

a node has
    - id
    - name
    - welcome text
    - zero or one theme

the active node is selected by a sysop and if the selected node contains a theme then this overrides any used selected theme

examples

    node:
        name: christmas
        theme: christmas
        welcome message: 'ho ho ho'

        name: halloween
        theme: halloween
        welcome message: 

        name: default
        theme: none
        welcome message: 

There are those who believe that spodding here began out there, far across the network, with tribes of users who may have been the forefathers of the Prestoneites, or the Facebookers, or the Twitters.

That they may have been the architects of the great forums, or the lost civilizations of Monochrome or anonyMUD. Some believe that there may yet be brothers of man who even now fight to survive somewhere beyond the screen…

administrations get an admin section

/admin
    - select mode
    - edit mode fields
    - save
    - create new mode


the db seed OR nexus install should create the default node

test
    - normal user cannot visit /admin
    - sysop can visit /admin
    

forms

    we should have a way to name the multiple forms which could be on a page

    Sections
        "Section{$section->id}"

    Posts
        "Post{$post->id}"

clean up the names of the forms we have

Post

Section




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