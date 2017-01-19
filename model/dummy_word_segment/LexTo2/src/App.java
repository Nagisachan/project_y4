import java.io.BufferedReader;
import java.io.File;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.ArrayList;
import java.util.List;

import org.apache.commons.logging.Log;
import org.apache.commons.logging.LogFactory;

public class App {

	private static final Log log = LogFactory.getLog(App.class);
	
	private File dictFile;
	private final int port = 6789;
	
	public App(String[] args) {
		
		dictFile = new File(args[0]);
		
		try(ServerSocket serverSocket = new ServerSocket(port)) {
			log.info("start listen on port " + port + "...");
			while(true){
				new SocketHandler(serverSocket.accept()).start();
			}
		} catch (Exception e) {
			log.error("error in main",e);
		}
	}
	
	public static void main(String[] args) {
		if(args.length != 1){
			System.err.println("Usage LongLexTo <dict-path>");
			System.exit(-1);
		}
		
		new App(args);
	}

	class SocketHandler extends Thread{
		
		private Socket socket;
		
		public SocketHandler(Socket socket) {
			this.socket = socket;
		}
		
		@Override
		public void run() {
			try{
				LongLexTo tokenizer = new LongLexTo(dictFile);
				File unknownFile = new File(dictFile.getParent(),"unknown.txt");
				if (unknownFile.exists())
					tokenizer.addDict(unknownFile);

				String line;
				int begin, end;

				BufferedReader br = new BufferedReader(new InputStreamReader(socket.getInputStream()));
				PrintWriter pw = new PrintWriter(socket.getOutputStream(),true);
				line = br.readLine().trim();
				
				List<String> words = new ArrayList<>();
				if (!line.isEmpty()) {
					tokenizer.wordInstance(line);
					begin = tokenizer.first();

					while (tokenizer.hasNext()) {
						end = tokenizer.next();
						String word = line.substring(begin, end);
						if (!word.trim().isEmpty()) {
							words.add("\"" + word + "\"");
						}
						begin = end;
					}
				}

				String output = String.join(",", words);
				
				log.info(line);
				log.info(output);
				
				pw.println(output);
				
				br.close();
				pw.close();
			}
			catch(Exception e){
				log.error("error in handler",e);
			}
		}
		
	}
}
