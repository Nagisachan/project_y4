import java.io.*;
import java.net.HttpURLConnection;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;
import java.sql.*;
import java.util.Properties;
import org.json.*;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.entity.StringEntity;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.impl.client.HttpClientBuilder;

public class ParagraphSplitter {
	
	private static String connectionUrl = "jdbc:impala://10.35.23.230:21050/autotag";
	private static String jdbcDriverName = "com.cloudera.impala.jdbc4.Driver";
	public static void main(String[] args) throws FileNotFoundException, IOException, UnsupportedEncodingException, SQLException, InstantiationException, IllegalAccessException, ClassNotFoundException, JSONException {
		
		File fileDir = new File("D:\\Course 4th year\\Senior project\\testJava.txt");
		BufferedReader in = new BufferedReader(
		new InputStreamReader(new FileInputStream(fileDir), "UTF8"));
		String str;
		int i = 1;
		Class.forName(jdbcDriverName).newInstance();
		StringBuilder paragraph = new StringBuilder();
		Class.forName(jdbcDriverName);
	//	Connection con = DriverManager.getConnection(connectionUrl);
	//	Statement stmt = con.createStatement();
		JSONObject json = new JSONObject();
		JSONArray content = new JSONArray();
		//Connection conn = DriverManager.getConnection(connectionUrl);
	//	Statement st = conn.createStatement();
		while ((str = in.readLine()) != null) {
			if(!str.trim().isEmpty()){
				/* //check last char of each line
				System.out.println("Last char of line " + i + ": "+str.charAt(str.length() - 1));
				int ascii = str.charAt(str.length() - 1);
				System.out.println("ASCII: " + ascii );
				 */
				//replace  √–Õ”
				str = str.replaceAll(" “","”");
				str = str.replaceAll(" Ë“", "Ë”");
				str = str.replaceAll(" È“", "È”");
				str = str.replaceAll(" Í“", "Í”");
				str = str.replaceAll(" Î“", "Î”");
				//str = str.replaceAll("\"","");
			
				//System.out.println(str);
				paragraph.append(str);
				if(str.charAt(str.length() - 1) == ' ') {
					String insert = new String();
					insert = paragraph.toString();
					//System.out.println("Paragraph "+i+" : "+insert);
					//stmt.executeUpdate("INSERT INTO testInsertJDBC values (" + i + ", \"" + insert + "\")");
					i++;
					content.put(insert);
					paragraph.setLength(0);
					//st.executeUpdate("INSERT INTO testStoreText (content) values ('" + insert + "')");
				}
			}
		}
		in.close();
		json.put("count",i-1);
		json.put("content",content);
		System.out.println(json.toString());
		URL object=new URL("http://localhost/test.php");

		HttpURLConnection con = (HttpURLConnection) object.openConnection();
		con.setDoOutput(true);
		//con.setDoInput(true);
		con.setRequestProperty("Content-Type", "application/json; charset=UTF-8");
		con.setRequestProperty("Accept", "application/json; charset=UTF-8");
		con.setRequestMethod("POST");
		OutputStream os = con.getOutputStream();
		String param = json.toString();
		os.write(param.getBytes("UTF-8"));
		
		//OutputStream os = con.getOutputStream();
	    //DataOutputStream wr = new DataOutputStream(con.getOutputStream());
	    //wr.write(new String("json=" + json).getBytes());
	    //String param =  URLEncoder.encode(json.toString(), "UTF-8");
	    //wr.write(param.getBytes());
		os.flush();
		os.close();
		

		
        BufferedReader br = new BufferedReader(new InputStreamReader(
               (con.getInputStream())));

       String output;
        System.out.println("Output from Server .... \n");
        while ((output = br.readLine()) != null) {
            System.out.println(output);
        }
        con.disconnect();
/*
		//System.out.println("\n== Begin Query Results ======================");
		ResultSet rs = stmt.executeQuery("SELECT content FROM testInsertJDBC");
		// print the results to the console
		while (rs.next()) {
			// the example query returns one String column
			System.out.println(rs.getString(1));
		}
		//System.out.println("== End Query Results =======================\n\n");
*/
/*		ResultSet rs = st.executeQuery("SELECT * FROM testStoreText");
	    if (st.execute("SELECT * FROdM testStoreText")) {
	        rs = st.getResultSet();
	        ResultSetMetaData rsmd = rs.getMetaData();
	        int columnsNumber = rsmd.getColumnCount();
	        while (rs.next()) {
	            for (int j = 1; j <= columnsNumber; j++) {
	                if (j > 1) System.out.print(",  ");
	                String columnValue = rs.getString(i);
	                System.out.print(columnValue + " " + rsmd.getColumnName(i));
	            }
	            System.out.println("");
	        }
	    }*/
	}
}
