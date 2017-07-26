# viz
**Simple dashboard builder written in PHP that uses MySQL, Bootstrap, and D3/NVD3 for Data visualizations.**

![Dashboard Image](https://owlhousellc.com/oh_sample1.png)

**Mobile Ready!**

![Mobile Image](https://owlhousellc.com/oh_sample3.png) ![Mobile Image](https://owlhousellc.com/oh_mobile_sm.gif)

**Live Demo available at:
     https://owlhousellc.com/livedemo**

## Installation Notes:
Using MySQL, create the *viz* database and associated credentials.  Next, run *install_viz.sql* found in the *install* directory.  Make sure to include your connection information and credentials in the *config.php* file.

The default admin credentials are as follows:

     username: admin
     password: admin

The default editor credentials are as follows(no user admin access):

     username: edit
     password: edit

The default view-only credentials are as follows(no edit or user admin access):

     username: demo
     password: demo


## Sample Data Installation Notes (Optional):
For examples/sample data, create a database named *sampledb* and run the *sampledb.sql*.  Next, run the *sample_tabs.sql* and *sample_visualizations.sql* on the *viz* database.  The visualizations will need to be updated with the correct connection/authentication information by clicking on the cog icon, going in to Manage Visualizations, and editing each.

Once the installation is complete, the *install* directory should be deleted.


## Managing Tabs:
Name: The name must be a unique html object name (no spaces or special characters)

Title: Name displayed on the screen

Position: Indicates the order in which the tabs are displayed

Notes: This section is for notes


## Managing Visualizations:
Name: The name must be a unique html object name (no spaces or special characters)

Title: Name displayed on the screen

Tab: Tab in which you would like visualization to appear

Position: Position on tab which the visualization will appear

Size: Bootstrap width (col-md-6 or col-md-12)

Class: Custom class for visualization

Type: Visualization type (line, bar, scatter, etc.)

DBType: Database Type (MySQL or MS-SQL)

DBHost: Database server name or IP (MS-SQL may require instance name)

DBName: Database name

DBUser: Database username

DBPass: Database password

Query: SQL query (or other "query" information for the visualization type)

Height Override: Customize visualization height (Default 250px)

Date Format Override: D3 date format settings (Default %Y-%m-%d)

Notes: This section is for notes

*(Additional Note:  In general, the first column in the query result will be the x-axis.  The query may need to be adjusted according to the desired outcome.)*
