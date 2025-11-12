# oc3-sengine

=== OC3 Search engine ===
Name: OC3 Search engine
Contributors: olevacho 
Tags: AI, Semantic search, search, RAG, AI embedding
Requires at least: 5.6 
Tested up to: 6.7
Requires PHP: 7.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.0.1

Semantic search of website content with meaning...

== Description ==

Helps to create search box with semantic search of website content

### Features
* AI powered search
* Content aware search
* Personalize the appearance of the search box: colors, styles, text, search result
* Ability to choose whether the search field will be visible only to registered visitors or not.

 



== Installation ==

### Quick setup:
-Open account at [OpenAI](https://platform.openai.com/signup) and get API key. 
-Open account at [pinecone](https://www.pinecone.io/). Create empty Index there,  set the dimension to 1536, and choose cosine for the metric.
-Get your API KEY from https://www.pinecone.io/
-Go to <YOUR_WEBSITE_URL>/wp-admin/admin.php?page=oc3sengine_settings page of this plugin. Input copied API key in the 'Open AI Key:' field and Pinecone Key into  'Pinecone API Key' field
- Click Sync Indexes button
- Select Pinecode Index
- Select Embedding model 
- Select other parameters if you need
- Click Save button 
- Switch to Pinecone tab.
- Select the posts you want to use as content for semantic search. Click the play button next to each post to add its content to the vector database.
-You can customize  appearance, behavior of the search box on configuration page <YOUR_WEBSITE_URL>/wp-admin/admin.php?page=oc3sengine_search. 
-put [oc3-sengine] shortcode to any page/post you need

== External Services ==
This plugin uses external API when it performs following functions:
-when it runs semantic search it sends requests to vector databases:  [Pinecone](https://www.pinecone.io/)
-when it builds vector database from content of your website it sends content selected by you to vector database APIs:[Pinecone](https://www.pinecone.io/)
-when you select delete indexed content then it sends deletion requests to APIs:  [Pinecone](https://www.pinecone.io/)
-when clients of your website try to search information then plugin sends user's request to [OpenAI](https://openai.com/) API for build embedding and then sends embedded request to vector database APIs for doing semantic search:[Pinecone](https://www.pinecone.io/)
 By using this plugin, you consent to sending data to OpenAI's and/or Pinecone's servers, which may include user queries and other relevant information.
Please ensure compliance with OpenAI’s & Pinecone terms and any applicable data privacy laws.

- **Service Providers**:
  - [OpenAI](https://openai.com/)
  - [Pinecone](https://www.pinecone.io/product/)
- **Terms of Use**:
  - [OpenAI API Terms](https://openai.com/policies/terms-of-use/)
  - [Pinecone](https://www.pinecone.io/legal/)
- **Privacy Policies**:
  - [OpenAI Privacy Policy](https://openai.com/policies/privacy-policy/)
  - [Pinecone](https://www.pinecone.io/privacy/)


== Open AI ==

The OC3 Sengine makes use of the API provided by [OpenAI](https://openai.com/blog/openai-api or [Reference]https://platform.openai.com/docs/api-reference). This plugin does not collect any data from your OpenAI account apart from the number of tokens used. The information sent to the OpenAI servers mainly includes the content of your article and the specified context. The usage information displayed in the add-on's settings is only for your reference. To obtain accurate information about your usage, it is important to check it on the [OpenAI website](https://platform.openai.com/account/usage). Additionally, please make sure to review their [Privacy Policy](https://openai.com/privacy/) and [Terms of Service](https://openai.com/terms/) for more details.

== Pinecone vector database==

The OC3 Sengine makes use of the API provided by [Pinecone](https://www.pinecone.io/product/). This plugin does not collect any data from your Pinecone account. The information sent to the Pinecone servers mainly includes the content of your website and the specified context. The usage information displayed in the add-on's settings is only for your reference. To obtain accurate information about your usage, it is important to check it on the [Pinecone website](https://www.pinecone.io/learn/). Additionally, please make sure to review their [Privacy Policy](https://www.pinecone.io/privacy/) and [Legal documents](https://www.pinecone.io/legal/) for more details.

== Disclaimer ==


The OC3 Sengine is a plugin that allows users to integrate their websites with AI services such as OpenAI's ChatGPT, Pinecone vector database https://www.pinecone.io/. In order to use this plugin, users must have their own API keys and adhere to the guidelines provided by the chosen AI services and or vector database services. When utilizing the OC3 Sengine, users are required to monitor and oversee the content produced by the AI or vector database, as well as handle any potential issues or misuse. The developer of the OC3 Sengine plugin and other related parties cannot be held responsible for any problems or losses that may arise from the usage of the plugin or the content generated by the AI and/or by vector database services. Users are advised to consult with a legal expert and comply with the applicable laws in their jurisdiction. OpenAI, ChatGPT, and related marks are registered trademarks of OpenAI. Author of this plugin is not a partner of, endorsed by, or sponsored by OpenAI. Also,  author of this plugin is not a partner of, endorsed by, or sponsored by Pinecone Systems, Inc.


== Screenshots ==
1. General settings of the plugin.
2. Pinecone settings
3. Searchbox styles settings
4. Search box with search results



== Changelog ==

= 1.0.1 =
* Launch!
