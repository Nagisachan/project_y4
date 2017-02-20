import java.io.BufferedReader;
import java.io.File;
import java.io.FileOutputStream;
import java.io.FileReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.ArrayList;
import java.util.Hashtable;
import java.util.List;
import java.util.Vector;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

public class App {

	private static final Log log = LogFactory.getLog(App.class);

	private File dictFile, unknownFile;
	private List<String> exceptionList;
	private Hashtable<Integer, String> cache;
	private final int port = 6789;
	Trie dict;

	public App(String[] args) {

		dictFile = new File(args[0]);
		unknownFile = new File(args[1]);
		File exceptionFile = args.length >= 3 ? new File(args[2]) : null;
		if(exceptionFile != null && exceptionFile.exists()){
			try (BufferedReader br = new BufferedReader(new FileReader(exceptionFile))) {
				String line;
				exceptionList = new ArrayList<>();
				while((line = br.readLine()) != null){
					if(!line.trim().isEmpty()){
						exceptionList.add(line.trim());
					}
				}
				
				log.info(exceptionList.size() + " exception words.");
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		
		dict = new Trie();
		cache = new Hashtable<>();

		if (dictFile.exists()) {
			try {
				addDict(dictFile);
			} catch (IOException e) {
				log.error("error in add dict", e);
			}
		} else {
			System.out.println(" !!! Error: The dictionary file is not found, " + dictFile.getName());
		}

		try (ServerSocket serverSocket = new ServerSocket(port)) {
			log.info("start listen on port " + port + "...");
			while (true) {
				new SocketHandler(serverSocket.accept()).start();
			}
		} catch (Exception e) {
			log.error("error in main", e);
		}
	}

	private void addDict(File dictFile) throws IOException {

		// Read words from dictionary
		String line;
		FileReader fr = new FileReader(dictFile);
		BufferedReader br = new BufferedReader(fr);

		while ((line = br.readLine()) != null) {
			line = line.trim();
			if (line.length() > 0)
				dict.add(line);
		}

		br.close();
	}

	public static void main(String[] args) {
		if (args.length < 2) {
			System.err.println("Usage LongLexTo <dict-path> <unknown-path> [exception]");
			System.exit(-1);
		}

		new App(args);
	}

	class SocketHandler extends Thread {

		private Socket socket;

		public SocketHandler(Socket socket) {
			this.socket = socket;
		}

		@Override
		public void run() {
			try {
				log.info("new connection from " + socket.getInetAddress() + "...");

				String line;
				int begin, end;

				BufferedReader br = new BufferedReader(new InputStreamReader(socket.getInputStream()));
				PrintWriter pw = new PrintWriter(socket.getOutputStream(), true);
				line = br.readLine().trim();

				String output;
				int hashCode = line.hashCode();
				
				if (cache.containsKey(hashCode)) {
					output = cache.get(hashCode);
					log.debug(hashCode + ": from cache");
				} else {
					LongLexTo tokenizer = new LongLexTo(dict);
					File unknownFile = new File(dictFile.getParent(), "unknown.txt");
					if (unknownFile.exists())
						tokenizer.addDict(unknownFile);
					
					PrintWriter pwUnknown = new PrintWriter(new FileOutputStream(App.this.unknownFile, true));
					
					List<String> words = new ArrayList<>();
					
					if (!line.isEmpty()) {
						if(isExceptionWord(line)){
							words.add(line);
						}
						else{
							tokenizer.wordInstance(line);
							begin = tokenizer.first();
							
							Vector<Integer> typeList = tokenizer.getTypeList();
							int i=0;
							int type;
							while (tokenizer.hasNext()) {
								type=((Integer)typeList.elementAt(i++)).intValue();
								
								end = tokenizer.next();
								String word = line.substring(begin, end);
								if (!word.trim().isEmpty()) {
									words.add("\"" + word + "\"");
									if(type == 0){
										pwUnknown.println(word);
									}
								}
								begin = end;
							}
						}
					}
					
					pwUnknown.close();
					output = String.join(",", words);
					cache.put(line.hashCode(), output);
					log.debug(hashCode + ": from lexto");
				}

				log.info(line);
				log.info(output);

				pw.println(output);
				
				br.close();
				pw.close();
			} catch (Exception e) {
				log.error("error in handler", e);
			}
		}

		private boolean isExceptionWord(String word){
			if(exceptionList == null){
				return false;
			}
			
			for(String s : exceptionList){
				if(s.equals(word)){
					return true;
				}
			}
			
			return false;
		}
	}
}
