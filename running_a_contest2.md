# Introduction #

Contest code has been overhauled.  Contest now broken down into separate divisions such as "compo;open;gamejam".  Contestants in separate divisions can get voting results in the SAME categories.  So if you want a separate overall category for the Compo and the Open division, you should name the cats like "OverallCompo" and "OverallOpen" or whatever.

# Announcing it #

  1. create a category for it "ld14" or whatever - for mini compos, create a sub category of the mini category
  1. e-mail the gamecompo.com mailing list of course!
  1. make a blog post announcing the upcoming contest (as user=news)
  1. For a big compo, copy the previous compo's wiki to a new folder and update some links so that each compo gets its own wiki.  More specific instructions to be added.

# The Countdown #

  1. Modify https://ludumdare.googlecode.com/svn/trunk/compo/themes/ludum/countdown.php
  1. SSH to the site
  1. $ cd (something)/compo/wp-content/themes/ludum
  1. $ svn update

# Theme Voting #

  1. Create a new wordpress "page" per day of theme voting
  1. Fill that page with the theme voting tag as follows.  To start voting:

```
[compo-vote:open:theme1;theme 2;theme 3]
```

  * To show the results of voting:

```
[compo-vote:closed:theme1;theme 2;theme 3]
```

# Starting a Compo #

  1. Create a new wordpress page for the compo
  1. Put in a tag .  This is a basic tag for a Official Compo that includes a Game Jam

```
[compo2 state="active" jcat="ld18" init="1"  calc_droplow="2" calc_drophigh="2" calc_reqvote="3" divs="compo;open;gamejam" opendivs="compo;open;gamejam" compo_cats="Innovation;Fun;Overall" compo_title="Competition" compo_summary="The Basic <a href='#'>rules</a> apply"  open_cats="Overall" open_title="Open" open_summary="The open rules apply" gamejam_summary="No rules, do whatever" gamejam_title="Game Jam" topcat="Overall"]
```

For a Mini-LD, including a Game Jam is probably silly.  (Though in many cases, the Mini LD is ONLY a Game Jam, either way, don't include the Game Jam.  To run a Mini as just a Game Jam, just don't have a "rate" and "results" phase, just jump right to "closed" at the end.)

```
[compo2 state="active" jcat="ld18mini" init="1"  divs="compo" opendivs="compo" compo_cats="Innovation;Fun;Overall" compo_title="Competition" compo_summary="The Basic <a href='#'>rules</a> apply"  topcat="Overall"]
```

Here are all the compo2 tag attributes.

  1. **state** - is "active" at start.  Further definition below.
  1. **jcat** - is optional, but can refer to the wp-category slug that is being used by contestants to journal in.
  1. **divs** - a semi-colon delimited list of the divisions in this contest "compo;open;gamejam"
  1. **opendivs** - a semi-colon delimited list of divisions that can be joined at this time "compo;open;gamejam" when the compo goes into rate mode, probably change this to "open;gamejam"
  1. **$DIV\_cats** - should be a semi-colon delimited list of voting categories "Fun;Innovation;Production;Theme"
  1. **$DIV\_title** - title of the division "Competition" "Open" "Game Jam"
  1. **$DIV\_summary** - Summary / Rules: "The basic rules must be followed, etc, etc blah blah" May include HTML that uses single quotes, so you can link to more detailed information.
  1. **locked** - should be 0 or 1.  If set to 1, it will stop users from adding new entries.  Existing users can still edit their existing entries.  This setting restricts users from changing their division as well.
  1. **pubvote** - should be set to 1 if the public (non-entrants) can vote
  1. **calc\_droplow** - number of low votes to be dropped per entry
  1. **calc\_drophigh** - number of high votes to be dropped per entry
  1. **calc\_reqvote** - number of remaining votes required to calculate an average
  1. **topcat** - The voting category that will show the top entries, usually Overall.

NOTE: if you change calc\_droplow, calc\_drophigh, calc\_reqvote you must access ?admin=1&action=recalc to recalculate the results of the competition to reflect those changes.

NOTE: You can preview the results by accessing ?admin=1&action=results OR ?admin=1&action=top

# Rating Entries #
  1. Change the STATE to "rate"

# Viewing Results #
This process takes a few steps.

  1. ?admin=1&action=recalc
> This will force the site to recalc the results.
  1. ?admin=1&action=results
> View the results.
  1. ?admin=1&action=results&more=1
> To view the full results.
  1. ?admin=1&action=top
> View the "top" results.  Then do it for each &cat=NAME\_OF\_CATEGORY to build up the cache.  You can do that by clicking from the main results page.
  1. Change the STATE to "results"

# Closed Compos #
  1. Change the STATE to "closed"
This is only used for compos that are not being judged.  It merely closes the compo from adding new entries.

# Admin #
  1. Click on the "Enter admin mode" link.  It will let you modify and disable entries.
  1. action=results - see compo results
  1. action=top - see compo top results
  1. add ?cache=0 to reset the cache for a page

# Whatever #
# ?action=me
Redirect the user to ?action=preview&uid=UID

# Emergency Cache Mode #
Edit wp-content/plugins/compo2/fcache.php .. The first few lines let you put the site into emergency mode.  This should reduce the load on the site considerably.  There are 4 steps:

Step 1: in _compo2\_fcache\_admin add your IP address to the array.  So instead of array("127.0.0.1") it will say array("127.0.0.1","your\_ip\_address\_goes\_here");_

Step 2: you will make "_compo2\_fcache\_emergency" return true to enable the emergency mode._

Step 3: Do your emergency stuff.

Step 4: Undo steps 1 and 2.