# Introduction #

Add your content here.  Or not.

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
[compo2 state="active" jcat="ld234" cats="Innovation;Fun;Theme;Graphics;Audio;Humor;Overall;Community" rules="http://www.ludumdare.com/compo/rules/" gamejam="1"]
```

For a Mini-LD, including a Game Jam is probably silly.  (Though in many cases, the Mini LD is ONLY a Game Jam, either way, don't include the Game Jam.  To run a Mini as just a Game Jam, just don't have a "rate" and "results" phase, just jump right to "closed" at the end.)

```
[compo2 state="active" jcat="ld234" cats="Innovation;Fun;Overall" rules="link-to-main-blog-post"]
```

Here are all the compo2 tag attributes.

  1. **state** - is "active" at start.  Further definition below.
  1. **jcat** - is optional, but can refer to the wp-category slug that is being used by contestants to journal in.
  1. **cats** - should be a semi-colon delimited list of voting categories "Fun;Innovation;Production;Theme"
  1. **locked** - should be 0 or 1.  If set to 1, it will stop users from adding new entries.  Existing users can still edit their existing entries.
  1. **rules** - should be a URL to a page with rules for this competition.
  1. **gamejam** - should be set to 1 if this competition includes a Game Jam.
  1. **pubvote** - should be set to 1 if the public (non-entrants) can vote

# Rating Entries #
  1. Change the STATE to "rate"
NOTE: if gamejam=1, all new entries will be part of the gamejam at this point.

# Viewing Results #
  1. Change the STATE to "results"

# Closed Compos #
  1. Change the STATE to "closed"
This is only used for compos that are not being judged.  It merely closes the compo from adding new entries.

# Admin #
  1. Click on the "Enter admin mode" link.  It will let you modify and disable entries.