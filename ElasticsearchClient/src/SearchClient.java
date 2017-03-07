import static org.elasticsearch.common.xcontent.XContentFactory.jsonBuilder;

import java.io.BufferedReader;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.InetAddress;
import java.net.UnknownHostException;
import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;
import org.elasticsearch.action.admin.indices.create.CreateIndexRequestBuilder;
import org.elasticsearch.action.admin.indices.create.CreateIndexResponse;
import org.elasticsearch.action.admin.indices.delete.DeleteIndexRequest;
import org.elasticsearch.action.admin.indices.delete.DeleteIndexResponse;
import org.elasticsearch.action.admin.indices.exists.indices.IndicesExistsResponse;
import org.elasticsearch.action.bulk.BulkRequestBuilder;
import org.elasticsearch.action.bulk.BulkResponse;
import org.elasticsearch.client.Client;
import org.elasticsearch.client.transport.TransportClient;
import org.elasticsearch.common.transport.InetSocketTransportAddress;
import org.elasticsearch.common.xcontent.XContentBuilder;
import com.google.gson.JsonArray;
import com.google.gson.JsonElement;
import com.google.gson.JsonObject;
import com.google.gson.JsonParser;

/**
 * @author X
 *
 */
public class SearchClient {

	private static final Log log = LogFactory.getLog(SearchClient.class);

	Client client;

	private long reIndexCount;
	private int amountFetchedkeys;
	private long startTimeReIdex;
	private boolean doReindexing = false;
	private String host;

	public SearchClient(String host) {
		this.host = host;
		initElasticSearch();
	}

	private void initElasticSearch() {
		try {
			client = TransportClient.builder().build().addTransportAddress(new InetSocketTransportAddress(InetAddress.getByName(host), 9300));
		} catch (UnknownHostException e) {
			e.printStackTrace();
		}
	}

	private String readMockFile(){
		try (BufferedReader br = new BufferedReader(new InputStreamReader(new FileInputStream("fileforelastic.txt")))){
			String line;
			String content = "";
			
			while((line = br.readLine()) != null){
				content += line;
			}
			
			return content;
		} catch (IOException e) {
			e.printStackTrace();
		}
		
		return null;
	}
	
	public String reIndexSearchKey() {
		if (doReindexing) {
			return "reindexing, please wait until the current indexing finish...";
		}

		doReindexing = true;
		String index = "tagger-bot";
		String type = "paragraph";
		reIndexCount = 0;
		amountFetchedkeys = 0;
		startTimeReIdex = System.currentTimeMillis();

		try {
			log.info("fetching all keys for indexing...");

			JsonArray allParagraph = new JsonParser().parse(readMockFile()).getAsJsonArray();

			if (isIndexExists(index)) {
				DeleteIndexResponse delete = client.admin().indices().delete(new DeleteIndexRequest(index)).actionGet();
				log.info("index has been deleted: " + index);
				if (!delete.isAcknowledged()) {
					log.error("Index wasn't deleted");
				}
			}

			CreateIndexRequestBuilder createIndexRequestBuilder = client.admin().indices().prepareCreate(index);

			// prepare index settings
			final XContentBuilder settings = jsonBuilder().startObject().startObject("index").startObject("analysis")
					.startObject("analyzer").startObject("analyzer_keyword").field("tokenizer", "keyword").endObject()
					.endObject().endObject().endObject().endObject();

			log.debug("index settings will be applied: " + settings.string());
			createIndexRequestBuilder.setSettings(settings);

			// mapping for the index
			final XContentBuilder mapping = jsonBuilder().startObject().startObject("paragraph").startObject("properties")
					.startObject("key").field("analyzer", "analyzer_keyword").field("type", "string").endObject()
					.endObject().endObject().endObject();
			log.debug("index settings will be applied: " + mapping.string());
			createIndexRequestBuilder.addMapping(type, mapping);

			final CreateIndexResponse res = createIndexRequestBuilder.get();

			if (!res.isAcknowledged()) {
				throw new IOException("Could not create index " + index);
			}

			BulkRequestBuilder bulkRequest = client.prepareBulk();

			for (int i=0;i<allParagraph.size();i++) {
				JsonObject data = allParagraph.get(i).getAsJsonObject();
				XContentBuilder obj = jsonBuilder().startObject();
				
				for (Map.Entry<String, JsonElement> entry: data.entrySet()) {
				    if(entry.getKey().equals("tags")){
				    	List<String> output = new ArrayList<>();
				    	JsonArray jary = entry.getValue().getAsJsonArray();
				    	for(int j=0;j<jary.size();j++){
				    		output.add(jary.get(j).getAsString());
				    	}
				    	obj.field(entry.getKey(), String.join(",", output));
				    }
				    else{
				    	obj.field(entry.getKey(), entry.getValue().getAsString());
				    }
				}
				
				obj.endObject();
				bulkRequest.add(client.prepareIndex(index, type).setSource(obj));
				reIndexCount++;
			}

			BulkResponse bulkResponse = bulkRequest.execute().actionGet();
			if (bulkResponse.hasFailures()) {
				return "Error occure when reindex: " + bulkResponse.buildFailureMessage();
			} else {
				return "Re-index " + reIndexCount + " keys using " + bulkResponse.getTookInMillis() + " ms.";
			}

		} catch (Exception e) {
			log.error("Can not re-index", e);
			e.printStackTrace();
			return e.getMessage();
		} finally {
			log.info("Indexed " + reIndexCount + " keys");
			doReindexing = false;
		}
	}

//	private XContentBuilder toSearchObject(String key, String value) throws IOException {
//		XContentBuilder searchObject;
//
//		if (key.equals(Mapping.PREFIX_FULLNAME)) {
//			String[] tmp = value.split(" ");
//			searchObject = jsonBuilder().startObject().startObject("query").startObject("query_string")
//					.field("analyze_wildcard", "true").field("default_operator", "AND")
//					.field("query",
//							"prefix:" + key + (tmp.length > 0 ? " key:*" + tmp[0] + "*" : "")
//									+ (tmp.length > 1 ? " key:*" + tmp[1] + "*" : ""))
//					.endObject().endObject().field("size", "1000000000").endObject();
//			return searchObject;
//		} else {
//			searchObject = jsonBuilder().startObject().startObject("query").startObject("query_string")
//					.field("analyze_wildcard", "true").field("default_operator", "AND")
//					.field("query", "prefix:" + key + " key:*" + value + "*").endObject().endObject()
//					.field("size", "1000000000").endObject();
//			return searchObject;
//		}
//
//	}

	public boolean isIndexExists(String indexName) {

		IndicesExistsResponse res = client.admin().indices().prepareExists(indexName).get();
		if (!res.isExists()) {
			log.info("Index does not exist yet: " + indexName);
			return false;
		} else {
			log.info("Index already exists.: " + indexName);
			return true;
		}

	}

	public long getReIndexCount() {
		return reIndexCount;
	}

	public int getAmountFetchedkeys() {
		return amountFetchedkeys;
	}

	public long getStartTimeReIdex() {
		return startTimeReIdex;
	}

	public boolean isDoReindexing() {
		return doReindexing;
	}

	public static void main(String[] args){
		new SearchClient("punyapat.org").reIndexSearchKey();
	}
	
}
