MLIB-Inventory-Server
=======================

Introduction
------------
Restful service for handling computer asset management.

Download
------------
It is best to clone this repo like so `git clone --recursive https://github.com/jas-/MLIB-Inventory-Server.git`

Installation
------------
To install simply run the `./install` file located within the 'install' folder

Virtual Host
------------
An example...

```
<VirtualHost *:80>
    ServerName inventory.dev
    DocumentRoot /var/www/html/MLIB-Inventory-Server/public
    SetEnv APPLICATION_ENV "development"
    <Directory /var/www/html/MLIB-Inventory-Server/public>
        DirectoryIndex index.php
        AllowOverride All
        Order allow,deny
        Allow from all
    </Directory>
    ErrorLog /var/log/httpd/inventory_error
    CustomLog /var/log/httpd/inventory_log common
</VirtualHost>
```

<!-- MLIB-Inventory-Server RESTFul API -->
<div id="overview">
  <h1>MLIB-Inventory-Server RESTFul API</h1>

  <p>
    This document serves as a guide during installation, configuration
    and implementation of the provided API for all asset record management
    functionality.
  </p>

  <p>
    This project works as the server component to the Marriott Library
    Information Services inventory management service. The offical project
    page can be found @ <a href="https://github.com/jas-/MLIB-Inventory-Server" target="_blank">github.com/jas-/MLIB-Inventory-Server</a>
  </p>
</div>

<!-- Table of contents -->
<div id="toc" class="jumbotron">
  <h2>Table of contents</h2>

  <p>
    <ol>
      <li><a href="#project"><i>Project details</i></a></li>
      <li><a href="#download"><i>Download</i></a>
        <ol>
          <li><a href="#download-git"><i>Using GIT</i></a></li>
          <li><a href="#download-git"><i>Manual</i></a></li>
        </ol>
      </li>
      <li><a href="#installation"><i>Installation</i></a>
        <ol>
          <li><a href="#installation-details"><i>Details</i></a></li>
          <li><a href="#installation-tools"><i>Installation tool</i></a></li>
          <li><a href="#installation-post"><i>Post installation</i></a></li>
        </ol>
      </li>
      <li><a href="#api"><i>API</i></a>
        <ol>
          <li><a href="#api-models"><i>Manage Model records</i></a>
            <ol>
              <li><a href="#api-models-list"><i>List all model records</i></a>
              <li><a href="#api-models-search"><i>Search model records</i></a>
              <li><a href="#api-models-add"><i>Add model records</i></a>
              <li><a href="#api-models-update"><i>Update model records</i></a>
              <li><a href="#api-models-delete"><i>Delete model records</i></a>
            </ol>
          </li>
          <li><a href="#api-computers"><i>Manage Computer records</i></a>
            <ol>
              <li><a href="#api-computers-list"><i>List all computer records</i></a>
              <li><a href="#api-computers-search"><i>Search computer records</i></a>
              <li><a href="#api-computers-add"><i>Add computer records</i></a>
              <li><a href="#api-computers-update"><i>Update computer records</i></a>
              <li><a href="#api-computers-delete"><i>Delete computer records</i></a>
            </ol>
          </li>
          <li><a href="#api-monitors"><i>Manage Monitor records</i></a>
            <ol>
              <li><a href="#api-monitors-list"><i>List all monitors records</i></a>
              <li><a href="#api-monitors-search"><i>Search monitor records</i></a>
              <li><a href="#api-monitors-add"><i>Add monitor records</i></a>
              <li><a href="#api-monitors-update"><i>Update monitor records</i></a>
              <li><a href="#api-monitors-delete"><i>Delete monitor records</i></a>
            </ol>
          </li>
          <li><a href="#api-rma"><i>Manage RMA records</i></a>
            <ol>
              <li><a href="#api-rma-list"><i>List all RMA records</i></a>
              <li><a href="#api-rma-search"><i>Search RMA records</i></a>
              <li><a href="#api-rma-add"><i>Add RMA records</i></a>
              <li><a href="#api-rma-update"><i>Update RMA records</i></a>
              <li><a href="#api-rma-delete"><i>Delete RMA records</i></a>
            </ol>
          </li>
          <li><a href="#api-cors"><i>Manage CORS records</i></a>
            <ol>
              <li><a href="#api-cors-list"><i>List all CORS records</i></a>
              <li><a href="#api-cors-search"><i>Search CORS records</i></a>
              <li><a href="#api-cors-add"><i>Add CORS records</i></a>
              <li><a href="#api-cors-update"><i>Update CORS records</i></a>
              <li><a href="#api-cors-delete"><i>Delete CORs records</i></a>
            </ol>
          </li>
        </ol>
      </li>
    </ol>
  </p>
</div>

<!-- Details -->
<div id="project">
  <h2>Project details <span><a href="#toc">&#8593;</a></span></h2>

  <p>
    The MLIB-Inventory-Server RESTFul API was developed to provide an
    easy to use inventory asset management service. It does this by
    providing an easy to implement API for any programming language
    that supports the HTTP protocol.
  </p>
</div>

<!-- Download instructions -->
<div id="download">
  <h2>Download <span><a href="#toc">&#8593;</a></span></h2>

  <p>
    This project allows for two methods of downloading, the GIT method
    (recommended) or the manual method. Please read on for details of each.
  </p>

  <!-- Using git -->
  <div id="download-git">
    <h3>Using GIT <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      A <a href="" target="_blank">git</a> client is the prefered method of
      installation as all dependencies will be setup and installed.

      <pre class="prettyprint lang-sh linenums prettyprinted">
        <code class="language-sh">
shell> git clone --recursive https://github.com/jas-/MLIB-Inventory-Server.git
        </code>
      </pre>
    </p>
  </div>

  <!-- Manual download -->
  <div id="download-manual">
    <h3>Manual Installation <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      Installing manually is not recommended however can be accomplished fairly
      easily by downloading the source of this project @
      <a href="https://github.com/jas-/MLIB-Inventory-Server/archive/master.zip" target="_blank">
      MLIB-Inventory-Server.zip</a>.

      <pre class="prettyprint lang-sh linenums prettyprinted">
        <code class="language-sh">
shell> unzip2 master.zip
        </code>
      </pre>
    </p>

    <p>
      Once you have downloaded the project you must extract it in a publicly
      accessible area of your web server.
    </p>

    <p>
      This project uses the Zend Framework which will need to be downloaded and
      installed into the <i>/path/to/MLIB-Inventory-Server/vendor/ZF2</i>
      folder. You can download the framework @
      <a href="https://github.com/zendframework/zf2/archive/master.zip" target="_blank">Zend Framework 2</a>
    </p>
  </div>
</div>

<!-- Installation section -->
<div id="installation">
  <h2>Installation <span><a href="#toc">&#8593;</a></span></h2>

  <p>
    Once you have downloaded or used git to install the base files we will now
    need to install the project.
  </p>
    
  <!-- Installation details -->
  <div id="installation-details">
    <h3>Details <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      Installation performs the following operations:
      <ol>
        <li>New database creation</li>
        <li>New accounts:
          <ul>
            <li>Administrative account (Read & Write privileges)</li>
            <li>Read Only account (Read only privileges)</li>
          </ul>
        </li>
        <li>Database table creation</li>
        <li>Foreign key constraints for relational tables</li>
        <li>Views for non-authenticated use</li>
        <li>Stored procedures for all database operations</li>
        <li>Application configuration based on user input</li>
      </ol>
    </p>
  </div>

  <!-- Installation tools -->
  <div id="installation-tools">
    <h3>Installation tool <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      In order for the installation to be quite painless an installer is
      included with the project. Please note that currently, becuase there is
      only one version released that should be considered 'beta' no method
      for updating database schema objects exists so you will want to ensure
      a good working backup has been created prior to running the installer.
  
      <pre class="prettyprint lang-sh linenums prettyprinted">
        <code class="language-sh">
shell> bash install/install
        </code>
      </pre>
    </p>
  </div>

  <!-- Post installation -->
  <div id="installation-post">
    <h3>Post installation <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      Due to the cross origin resource sharing same origin restrictions if you
      have setup the MLIB-Inventory (mobile web client) to access this
      service you must add that URL to the whitelist (if installed on a
      separate domain) to ensure proper functionality.
    </p>

    <p>
      Using a stored procedure (created during the installation process), simply
      issue the following command on the database.

      <pre class="prettyprint lang-sh linenums prettyprinted">
        <code class="language-sh">
shell> mysql -u &lt;username&gt; -p &lt;dbname&gt; -e 'CALL CorsAddUpdate("&lt;appName&gt;", "&lt;FQDN&gt;", "&lt;IP&gt;")'
        </code>
      </pre>
    </p>
</div>

<!-- Inventory management API -->
<div id="api">
  <h2>API <span><a href="#toc">&#8593;</a></span></h2>

  <p>
    This API will allow management of records through a common end point per
    record type.
  </p>

  <p>
    Below is a list of the available end points and a description
    of the record type.
  </p>

  <!-- Models API -->
  <div id="api-models">
    <h3>Models <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      Manage Models records. Below are the details of the available fields
      per Models record. <i>* Important: Computer, Monitor & RMA records all require an existing, matching model entry</i>

      <ol>
        <li><i>Model</i> - (Required) The model number associated with the montitor</li>
        <li><i>Notes</i> - (Optional) Any notes associated with the model</li>
        <li><i>Description</i> - (Optional) Any description information assocated with model</li>
      </ol>
    </p>

    <div id="api-models-list">
      <h3>List models <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To list all available models:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /model
          </code>
        </pre>
      </p>

      <p>
        Response example:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "Id": "591",
        "Model": "6200",
        "Notes": "",
        "Description": ""
    },
    {
        "Id": "734",
        "Model": "6350",
        "Notes": "",
        "Description": ""
    },
    /* ... */
]
          </code>
        </pre>
      </p>
    </div>

    <div id="api-models-search">
      <h3>Search models <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To search all available models (* is the wildcard):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /model/96*
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when searching for Models asset records
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "id": "590",
        "model": "965",
        "description": "",
        "notes": ""
    },
    /* ... */
]
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to search specified records'
}
          </code>
        </pre>
      </p>
    </div>

    <div id="api-models-add">
      <h3>Add models <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To add a new models:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
POST /model
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-models">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
model=&lt;model-number&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when adding a Models asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Models record sucessfully add'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Given parameters did meet validation requirements",
    "details": {
        "model": "Model value is invalid"
    }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to add specified record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- Models Update -->
    <div id="api-models-update">
      <h3>Update models <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To update an existing Models record:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
PUT /model/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-models">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
model=&lt;model-number&gt;&eowd=&lt;eowd-date&gt;&opd=&lt;opd-date&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when updating a Models asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully updated record'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Given parameters did meet validation requirements",
    "details": {
        "model": "Model value is invalid"
    }
}
          </code>
        </pre>
      </p>

      <p>No changes occured message
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  warning: 'No changes to models record occured'
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting models record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Could not edit record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- Models Delete -->
    <div id="api-models-delete">
      <h3>Delete models <span><a href="#toc">&#8593;</a></span></h3>
      <p>
        Example usage of removing Models records.
      </p>

      <h4>Usage example <span><a href="#toc">&#8593;</a></span></h4>
      <p>
        To remove an existing models:
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
DELETE /model/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>
      <p>
        A successful response when removing a Models asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully deleted record'
}
          </code>
        </pre>
      </p>

      <p>
        Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting models record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to delete specified record'
}
          </code>
        </pre>
      </p>
    </div>
  </div>

  <!-- Computers API -->
  <div id="api-computers">
    <h3>Computers <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      Manage Computers records. Below are the details of the available fields
      per Computers record.

      <ol>
        <li><i>Hostname</i> - (Optional) The unique identifier associated with the computer</li>
        <li><i>Model</i> - (Optional) The model number associated with the montitor (see <a href="#api-models">models</a>)</li>
        <li><i>SKU</i> - (Required) The SKU number</li>
        <li><i>UUIC</i> - (Optional) The UUIC number</li>
        <li><i>Serial</i> - (Required) The serial number</li>
        <li><i>EOWD</i> - (Optional) The end of warranty date</li>
        <li><i>OPD</i> - (Optional) The original purchase date</li>
        <li><i>Notes</i> - (Optional) Any notes associated with the model</li>
        <li><i>Description</i> - (Optional) Any description information assocated with model</li>
      </ol>
    </p>

    <div id="api-computers-list">
      <h3>List computers <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To list all available computers:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /computer
          </code>
        </pre>
      </p>

      <p>
        Response example:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "Id": "1779",
        "Hostname": "abc-123xyu123",
        "Model": "965",
        "SKU": "123",
        "UUIC": "123",
        "Serial": "123",
        "EOWD": "2013-06-02",
        "OPD": "2009-06-02",
        "Notes": "",
        "Description": ""
    },
    {
        "Id": "1572",
        "Hostname": "ACQ-01",
        "Model": "6200",
        "SKU": "017043",
        "UUIC": "034755",
        "Serial": "090705",
        "EOWD": "2013-06-20",
        "OPD": "2009-06-01",
        "Notes": "",
        "Description": ""
    },
    /* ... */
]
          </code>
        </pre>
      </p>
    </div>

    <div id="api-computers-search">
      <h3>Search computers <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To search all available computers (* is the wildcard):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /computer/GRPC-1*
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when searching for Computers asset records
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "Id": "1437",
        "Hostname": "GRPC-10",
        "Model": "970",
        "SKU": "016058",
        "UUIC": "040318",
        "Serial": "087957",
        "EOWD": "2013-06-02",
        "OPD": "2009-06-02",
        "Notes": "",
        "Description": ""
    },
    {
        "Id": "1452",
        "Hostname": "GRPC-11",
        "Model": "970",
        "SKU": "016020",
        "UUIC": "040280",
        "Serial": "087933",
        "EOWD": "2013-06-02",
        "OPD": "2009-06-02",
        "Notes": "",
        "Description": ""
    },
    /* ... */
]
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to search specified records'
}
          </code>
        </pre>
      </p>
    </div>

    <div id="api-computers-add">
      <h3>Add computers <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To add a new computers:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
POST /computer
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-computers">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
hostname=&lt;computer-name&gt;&model=&lt;model-number&gt;&sku=&lt;computer-sku&gt;&serial=&lt;computer-serial&gt;&eowd=&lt;computer-eowd&gt;&eopd=&lt;computer-opd&gt;&notes=&lt;computer-notes&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when adding a Computers asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Computers record sucessfully add'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Given parameters did meet validation requirements",
    "details": {
        "hostname": "Hostname value is invalid",
        "model": "Model value is invalid",
        "sku": "SKU value is invalid",
        "uuic": "UUIC value is invalid",
        "serial": "Serial value is invalid",
        "eowd": "EOWD value is invalid",
        "opd": "OPD value is invalid",
        "notes": "Notes value is invalid"
    }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to add specified record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- Computers Update -->
    <div id="api-computers-update">
      <h3>Update computers <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To update an existing Computers record:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
PUT /computers/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-computers">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
hostname=&lt;computer-name&gt;&model=&lt;model-number&gt;&sku=&lt;computer-sku&gt;&serial=&lt;computer-serial&gt;&eowd=&lt;computer-eowd&gt;&eopd=&lt;computer-opd&gt;&notes=&lt;computer-notes&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when updating a Computers asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully updated record'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Given parameters did meet validation requirements",
    "details": {
        "hostname": "Hostname value is invalid",
        "model": "Model value is invalid",
        "sku": "SKU value is invalid",
        "uuic": "UUIC value is invalid",
        "serial": "Serial value is invalid",
        "eowd": "EOWD value is invalid",
        "opd": "OPD value is invalid",
        "notes": "Notes value is invalid"
    }
}
          </code>
        </pre>
      </p>

      <p>No changes occured message
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  warning: 'No changes to computers record occured'
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting computers record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Could not edit record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- Computers Delete -->
    <div id="api-computers-delete">
      <h3>Delete computers <span><a href="#toc">&#8593;</a></span></h3>
      <p>
        Example usage of removing Computers records.
      </p>

      <h4>Usage example <span><a href="#toc">&#8593;</a></span></h4>
      <p>
        To remove an existing computers:
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
DELETE /computers/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>
      <p>
        A successful response when removing a Computers asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully deleted record'
}
          </code>
        </pre>
      </p>

      <p>
        Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting computers record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to delete specified record'
}
          </code>
        </pre>
      </p>
    </div>
  </div>

  <!-- Monitors API -->
  <div id="api-monitors">
    <h3>Monitors <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      Manage Monitors records. Below are the details of the available fields
      per Monitors record.

      <ol>
        <li><i>Hostname</i> - (Optional) The unique identifier associated with the monitor</li>
        <li><i>Model</i> - (Optional) The model number associated with the montitor (see <a href="#api-models">models</a>)</li>
        <li><i>SKU</i> - (Required) The SKU number</li>
        <li><i>Serial</i> - (Required) The serial number</li>
        <li><i>EOWD</i> - (Optional) The end of warranty date</li>
        <li><i>OPD</i> - (Optional) The original purchase date</li>
        <li><i>Notes</i> - (Optional) Any notes associated with the model</li>
        <li><i>Description</i> - (Optional) Any description information assocated with model</li>
      </ol>
    </p>

    <div id="api-monitors-list">
      <h3>List monitors <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To list all available monitors:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /monitor
          </code>
        </pre>
      </p>

      <p>
        Response example:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "Id": "478",
        "Hostname": "GRPC-01",
        "Model": "970",
        "SKU": "16250",
        "Serial": "ETLFA0W041115034C94323",
        "EOWD": "2013-06-02",
        "OPD": "2009-06-02",
        "Notes": "",
        "Description": ""
    },
    {
        "Id": "491",
        "Hostname": "GRPC-02",
        "Model": "980",
        "SKU": "16720",
        "Serial": "ETLGQ0D053205019898514",
        "EOWD": "2015-07-01",
        "OPD": "2012-07-01",
        "Notes": "",
        "Description": ""
    },
    /* ... */
]
          </code>
        </pre>
      </p>
    </div>

    <div id="api-monitors-search">
      <h3>Search monitors <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To search all available monitors (* is the wildcard):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /monitor/GRPC-1*
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when searching for Monitors asset records
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "Id": "480",
        "Hostname": "GRPC-10",
        "Model": "970",
        "SKU": "16244",
        "Serial": "ETLFA0W041113116544323",
        "EOWD": "2013-06-02",
        "OPD": "2009-06-02",
        "Notes": "",
        "Description": ""
    },
    {
        "Id": "494",
        "Hostname": "GRPC-11",
        "Model": "970",
        "SKU": "16252",
        "Serial": "ETLFA0W041115034B14323",
        "EOWD": "2013-06-02",
        "OPD": "2009-06-02",
        "Notes": "",
        "Description": ""
    },
    /* ... */
]
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to search specified records'
}
          </code>
        </pre>
      </p>
    </div>

    <div id="api-monitors-add">
      <h3>Add monitors <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To add a new monitors:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
POST /monitor
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-monitors">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
hostname=&lt;monitor-name&gt;&model=&lt;model-number&gt;&sku=&lt;monitor-sku&gt;&serial=&lt;monitor-serial&gt;&eowd=&lt;monitor-eowd&gt;&eopd=&lt;monitor-opd&gt;&notes=&lt;monitor-notes&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when adding a Monitors asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Monitors record sucessfully add'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Given parameters did meet validation requirements",
    "details": {
        "hostname": "Hostname value is invalid",
        "model": "Model value is invalid",
        "sku": "SKU value is invalid",
        "uuic": "UUIC value is invalid",
        "serial": "Serial value is invalid",
        "eowd": "EOWD value is invalid",
        "opd": "OPD value is invalid",
        "notes": "Notes value is invalid"
    }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to add specified record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- Monitors Update -->
    <div id="api-monitors-update">
      <h3>Update monitors <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To update an existing Monitors record:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
PUT /monitors/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-monitors">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
hostname=&lt;monitor-name&gt;&model=&lt;model-number&gt;&sku=&lt;monitor-sku&gt;&serial=&lt;monitor-serial&gt;&eowd=&lt;monitor-eowd&gt;&eopd=&lt;monitor-opd&gt;&notes=&lt;monitor-notes&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when updating a Monitors asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully updated record'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Given parameters did meet validation requirements",
    "details": {
        "hostname": "Hostname value is invalid",
        "model": "Model value is invalid",
        "sku": "SKU value is invalid",
        "uuic": "UUIC value is invalid",
        "serial": "Serial value is invalid",
        "eowd": "EOWD value is invalid",
        "opd": "OPD value is invalid",
        "notes": "Notes value is invalid"
    }
}
          </code>
        </pre>
      </p>

      <p>No changes occured message
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  warning: 'No changes to monitors record occured'
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting monitors record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Could not edit record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- Monitors Delete -->
    <div id="api-monitors-delete">
      <h3>Delete monitors <span><a href="#toc">&#8593;</a></span></h3>
      <p>
        Example usage of removing Monitors records.
      </p>

      <h4>Usage example <span><a href="#toc">&#8593;</a></span></h4>
      <p>
        To remove an existing monitors:
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
DELETE /monitors/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>
      <p>
        A successful response when removing a Monitors asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully deleted record'
}
          </code>
        </pre>
      </p>

      <p>
        Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting monitors record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to delete specified record'
}
          </code>
        </pre>
      </p>
    </div>
  </div>

  <!-- RMA API -->
  <div id="api-rma">
    <h3>RMA <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      Manage RMA records. Below are the details of the available fields
      per RMA record.

      <ol>
        <li><i>Date</i> - (Required) The date as YYYY-MM-DD</li>
        <li><i>Hostname</i> - (Required) The RMA hostname</li>
        <li><i>Model</i> - (Required) The model number (see <a href="#api-models">models</a>)</li>
        <li><i>SKU</i> - (Required) The SKU associated with the RMA</li>
        <li><i>UUIC</i> - (Optional) The UUIC associated with the RMA</li>
        <li><i>Serial</i> - (Required) The serial number associated with the RMA</li>
        <li><i>Part</i> - (Required) The part description</li>
        <li><i>Notes</i> - (Optional) Any additional notes associated with the RMA record</li>
      </ol>
    </p>

    <div id="api-rma-list">
      <h3>List rma <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To list all available rma:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /rma
          </code>
        </pre>
      </p>

      <p>
        Response example:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "Id": "1",
        "Problem": "0",
        "Date": "2014-04-15",
        "Hostname": "DIG-15",
        "Model": "6200",
        "SKU": "017034",
        "UUIC": "034746",
        "Serial": "090722",
        "Part": "Video",
        "Notes": ""
    },
    {
        "Id": "2",
        "Problem": "1",
        "Date": "2014-02-11",
        "Hostname": "DIG-18",
        "Model": "6200",
        "SKU": "017037",
        "UUIC": "034742",
        "Serial": "090712",
        "Part": "Motherboard",
        "Notes": ""
    },
    /* ... */
]
          </code>
        </pre>
      </p>
    </div>

    <div id="api-rma-search">
      <h3>Search rma <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To search all available rma (* is the wildcard):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /rma/2014-04-*
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when searching for RMA asset records
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "Id": "1",
        "Problem": "0",
        "Date": "2014-04-15",
        "Hostname": "DIG-15",
        "Model": "6200",
        "SKU": "017034",
        "UUIC": "034746",
        "Serial": "090722",
        "Part": "Video",
        "Notes": ""
    },
    /* ... */
]
          </code>
        </pre>
      </p>

      <p>Empty response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Whoopsie! No records found, perhaps a wildcard search may help (ex. computer-name*)"
}          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to search specified records'
}
          </code>
        </pre>
      </p>
    </div>

    <div id="api-rma-add">
      <h3>Add rma <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To add a new rma:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
POST /rma
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-rma">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
hostname=&lt;machine-name&gt;&model=&lt;model-number&gt;&sku=&lt;machine-sku&gt;&uuic=&lt;machine-uuic&gt;&serial=&lt;machine-serial&gt;&part=&lt;part-description&gt;&description=&lt;Description&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when adding a RMA asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'RMA record sucessfully add'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Given parameters did meet validation requirements",
    "details": {
        "hostname": "Hostname value is invalid",
        "model": "Model value is invalid",
        "sku": "SKU value is invalid",
        "uuic": "UUIC value is invalid",
        "Serial": "Serial value is invalid",
        "Part": "Part value is invalid",
        "Description": "Description value is invalid",
    }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to add specified record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- RMA Update -->
    <div id="api-rma-update">
      <h3>Update rma <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To update an existing RMA record:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
PUT /rma/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-rma">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
hostname=&lt;machine-name&gt;&model=&lt;model-number&gt;&sku=&lt;machine-sku&gt;&uuic=&lt;machine-uuic&gt;&serial=&lt;machine-serial&gt;&part=&lt;part-description&gt;&description=&lt;Description&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when updating a RMA asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully updated record'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
    "error": "Given parameters did meet validation requirements",
    "details": {
        "hostname": "Hostname value is invalid",
        "model": "Model value is invalid",
        "sku": "SKU value is invalid",
        "uuic": "UUIC value is invalid",
        "Serial": "Serial value is invalid",
        "Part": "Part value is invalid",
        "Description": "Description value is invalid",
    }
}
          </code>
        </pre>
      </p>

      <p>No changes occured message
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  warning: 'No changes to rma record occured'
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting rma record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Could not edit record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- RMA Delete -->
    <div id="api-rma-delete">
      <h3>Delete rma <span><a href="#toc">&#8593;</a></span></h3>
      <p>
        Example usage of removing RMA records.
      </p>

      <h4>Usage example <span><a href="#toc">&#8593;</a></span></h4>
      <p>
        To remove an existing rma:
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
DELETE /rma/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>
      <p>
        A successful response when removing a RMA asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully deleted record'
}
          </code>
        </pre>
      </p>

      <p>
        Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting rma record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to delete specified record'
}
          </code>
        </pre>
      </p>
    </div>
  </div>

  <!-- CORS API -->
  <div id="api-cors">
    <h3>CORS <span><a href="#toc">&#8593;</a></span></h3>

    <p>
      Manage CORS records. Below are the details of the available fields
      per CORS record.

      <ol>
        <li><i>Application</i> - (Required) The unique identifier associated with the
          allowed application</li>
        <li><i>URL</i> - (Required) The FQDN of the referring application</li>
        <li><i>IP</i> - (Required) The IP of the referring application</li>
      </ol>
    </p>

    <div id="api-cors-list">
      <h3>List cors <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To list all available cors:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /cors
          </code>
        </pre>
      </p>

      <p>
        Response example:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "id": "1",
        "application": "resource-name",
        "url": "http://fqdn.of.resource-name",
        "ip": "192.1.2.10"
    },
    {
        "id": "2",
        "application": "another-name",
        "url": "http://fqdn.of.another-name",
        "ip": "192.1.2.115"
    },
    /* ... */
]
          </code>
        </pre>
      </p>
    </div>

    <div id="api-cors-search">
      <h3>Search cors <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To search all available cors (* is the wildcard):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
GET /cors/resource*
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when searching for CORS asset records
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
[
    {
        "id": "1",
        "application": "resource-name",
        "url": "http://fqdn.of.resource-name",
        "ip": "192.1.2.10"
    },
    /* ... */
]
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to search specified records'
}
          </code>
        </pre>
      </p>
    </div>

    <div id="api-cors-add">
      <h3>Add cors <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To add a new cors:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
POST /cors
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-cors">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
application=&lt;application-name&gt;&url=&lt;FQDN&gt;&ip=&lt;ip-address&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when adding a CORS asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'CORS record sucessfully add'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    application: 'Application value is invalid'
    url: 'URL value is invalid'
    ip: 'IP value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to add specified record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- CORS Update -->
    <div id="api-cors-update">
      <h3>Update cors <span><a href="#toc">&#8593;</a></span></h3>

      <p>
        To update an existing CORS record:

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
PUT /cors/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <p>
        Serialized data (see <a href="#api-corss">required fields</a>):

        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
application=&lt;application-name&gt;&url=&lt;FQDN&gt;&ip=&lt;ip-address&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>

      <p>A successful response when updating a CORS asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully updated record'
}
          </code>
        </pre>
      </p>

      <p>Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    application: 'Application value is invalid'
    url: 'URL value is invalid'
    ip: 'IP value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>No changes occured message
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  warning: 'No changes to cors record occured'
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting cors record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Could not edit record'
}
          </code>
        </pre>
      </p>
    </div>

    <!-- CORS Delete -->
    <div id="api-cors-delete">
      <h3>Delete cors <span><a href="#toc">&#8593;</a></span></h3>
      <p>
        Example usage of removing CORS records.
      </p>

      <h4>Usage example <span><a href="#toc">&#8593;</a></span></h4>
      <p>
        To remove an existing cors:
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-sh">
DELETE /cors/&lt;record-id&gt;
          </code>
        </pre>
      </p>

      <h4>Response example(s):</h4>
      <p>
        A successful response when removing a CORS asset record
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  success: 'Successfully deleted record'
}
          </code>
        </pre>
      </p>

      <p>
        Invalid parameters response
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Given parameters did meet validation requirements',
  details: {
    id: 'ID value is invalid'
  }
}
          </code>
        </pre>
      </p>

      <p>Database record didn't exist
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Whoops, an error occured while deleting cors record'
}
          </code>
        </pre>
      </p>

      <p>Generic error
        <pre class="prettyprint lang-sh linenums prettyprinted">
          <code class="language-js">
{
  error: 'Unable to delete specified record'
}
          </code>
        </pre>
      </p>
    </div>
  </div>
</div>

