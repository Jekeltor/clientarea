/////////////////////////////////////////////////////////////////////////
//                                                                     //
//                       Created by Jake Hamblin                       //
//                           jakehamblin.com                           //
//                                                                     //
/////////////////////////////////////////////////////////////////////////


// Install Steps //
1. Upload the contents of the folder to the root directory of your domain 
(the folder is often called 'public_html' or your domain name).

2. Go to your webpanel (cPanel, CentOS Web Panel, or DirectAdmin) and 
create a new database. Keep track of the database name, username, and user 
password.

3. Inside of that database, insert the provided SQL file called "uploadme.sql".

4. Go back to where you uploaded the files, and edit "config.php".

5. Change the information inside of the single quotations on the lines with 
"// Change this line". The information for these lines will be from the 
information on step 2.

6. Make sure to change all of the settings within the config.

7. To grab the OAuth2 Client ID and Secret ID, please to go https://discord.com/developers/applications. Once there, create a new application. Once that has been complete, copy the Client ID and Client Secret to the respective variables. After doing that, go to "OAuth2" and add a redirect to the location of your client panel. Once done, make that your redirect URL below.

8. Enjoy the new website!


// Steps to add new item/order
1. Upload the file into the "files" folder. Make sure the file doesn't have any spaces.

2. Go to the main page and insert the information needed. The product name can be whatever you want it to be. The Discord ID is the ID of the user. You can get this by enabling your developer panel, and grabbing that ID. For the price, it can be whatever. For the file name, make it the name of the file uploaded into the "files" folder.

3. Have your client login to get the download.


If you would like to make a donation to me to help further my website development 
career, my information is provided below:

PayPal Email: jake@jakehamblin.com
PayPal.me: https://paypal.me/jekeltor