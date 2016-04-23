# Enventory
An Electronics Warehouse Inventory System (WMS) for every electrical engineer.

The pure php approach, along with porting to codeIgniter has been abandoned. I have decided to use laravel.
I will upload the new version when it is at least somewhat functional.

<s>
# TODO List
 - Current: Convert from file by file to REST API (views,controllers, etc.) (Partially Done)
 - Current: Convert old, obsolete MYSQL functions to PDO object functions.
 - Clean the directories
 - add more comments and doxygen comments
 - Add more functionality
 - Create a init.sql file so others can use the system.
 - Create a setup script to create folders and check for requirements.
 - Update FPDF
</s>

# Requirements
 1. Be able to handle various electrical componets that do not have a set number of attributes (Like transformers). Will use json.
 2. Be able to export (csv, excel, pdf) data
 3. Provide standard WMS functionality such as users w/ groups, auditing...
 4. Be completely free
