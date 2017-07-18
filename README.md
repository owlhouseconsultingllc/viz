# viz
Simple dashboard builder written in PHP that uses MySQL, Bootstrap, and D3/NVD3 for Data visualizations.

Live Demo available at the following Link:
     https://owlhousellc.com/livedemo

**Installation Notes:**
Using MySQL create the database and associated credentials, and then run the install_viz.sql found in the install folder.  Make sure to include your connection information and credentials in the *config.php* file.

The default admin credentials are as follows:

     username: admin
     password: admin

The default editor credentials are as follows(no user admin access):

     username: edit
     password: edit

The default view-only credentials are as follows(no edit or user admin access):

     username: demo
     password: demo

**Sample Data Installation Notes (Optional):**
For examples/sample data, create a database named sampledb and run the *sampledb.sql*.  Next, run the *tabs.sql* and *visualizations.sql* on the viz database.  The visualizations will need to be updated with the correct connection/authentication information by clicking on the cog icon, going in to Manage Visualizations, and editing each.

Once the installation is complete, the installation folder should be deleted.
