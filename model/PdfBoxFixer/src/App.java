import java.io.BufferedInputStream;
import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

public class App {

	File inFile;
	File outFIle;
	HashMap<String, String> replaceDict;
	
	public App(String inFilePath) throws Exception {
		inFile = new File(inFilePath);
		outFIle = new File(inFile.getParent(),"output.txt");
		replaceDict = new HashMap<>();
		readReplaceDict();
		
		BufferedInputStream bis = new BufferedInputStream(new FileInputStream(inFile));
		PrintWriter pw = new PrintWriter(outFIle);
		
		byte content[] = new byte[(int) inFile.length()];
		int read = bis.read(content);
		
		if(read != inFile.length()){
			System.err.println("invalid length...");
		}
		else{
			// replace using dictionary
			String strContent = new String(content,"UTF-8");
			strContent = fixWord(strContent);
			
			// fix using rules
			char[] data = strContent.toCharArray();
			List<Character> output = new ArrayList<>();
			for(int i=0;i<data.length;i++){
				fixChar(data, i);
			}
			
			for(int i=0;i<data.length;i++){
				if(data[i] != 0){
					output.add(data[i]);
				}
			}
			
			char[] out = charListToByte(output);
			
			System.out.println(new String(out));
			pw.print(out);
		}
		
		bis.close();
		pw.close();
	}

	private void readReplaceDict() {
		File replaceDictFile = new File("swap.dict");
		try (BufferedReader br = new BufferedReader(new InputStreamReader(new FileInputStream(replaceDictFile)))) {
			String read;
			while((read = br.readLine()) != null){
				String[] tmp = read.split(";");
				replaceDict.put(tmp[0], tmp[1]);
				System.out.println(tmp[0] + " => " + tmp[1]);
			}
		} catch (IOException e) {
			e.printStackTrace();
		}
	}

	private String fixWord(String strContent) {
		for(String word : replaceDict.keySet()){
			if(strContent.contains(word)){
				String replace = replaceDict.get(word);
				strContent = strContent.replaceAll(word, replace);
			}
		}
		
		return strContent;
	}
	
	private void fixChar(char[] data, int i) {
		try{
			if(data[i] == ' ' && data[i+1] == 'า'){
				data[i] = 0;
				data[i+1] = 'ำ';
			}
			else if(String.valueOf(data[i]).matches("[็-์]") && String.valueOf(data[i+1]).matches("[ุ-ู]")){
				swap(data, i,i+1);
			}
			else if(String.valueOf(data[i+1]).matches("[ิ-ื]") && String.valueOf(data[i]).matches("[็-์]")){
				swap(data, i,i+1);
			}
			else if(String.valueOf(data[i]).matches("[ั]") && String.valueOf(data[i+1]).matches("[ ]")){
				swap(data, i,i-1);
			}
			else if(String.valueOf(data[i]).matches("[ั]") && !String.valueOf(data[i+1]).matches("[ก-ฮ,็-์]")){
				swap(data, i,i-1);
			}
			else if(String.valueOf(data[i]).matches("[ำ]") && String.valueOf(data[i+1]).matches("[็-์]")){
				swap(data, i,i+1);
			}
			else if(String.valueOf(data[i]).matches("[ั]") && String.valueOf(data[i+1]).matches("[ก-ฮ]") && String.valueOf(data[i+2]).matches("[็-์]")){
				swap(data, i+1,i+2);
			}
			else if(String.valueOf(data[i]).matches("[า]") && String.valueOf(data[i+1]).matches("[็-์]")){
				swap(data, i,i+1);
			}
			else if(String.valueOf(data[i]).matches("[ ]") && String.valueOf(data[i+1]).matches("[็-์]")){
				swap(data, i,i+1);
			}
			else if(String.valueOf(data[i]).matches("[ใไ]") && String.valueOf(data[i+1]).matches("[็-์]")){
				swap(data, i,i+1);
			}
			else if(String.valueOf(data[i]).matches("[เ]") && String.valueOf(data[i+1]).matches("[ู]")){
				swap(data, i,i+1);
			}
		}
		catch(Exception e){
			System.err.println(e.getMessage());
		}
	}

	private void swap(char[] data, int i, int j) {
		char tmp = data[i];
		data[i] = data[j];
		data[j] = tmp;
	}

	private char[] charListToByte(List<Character> output) {
		char[] out = new char[output.size()];
		for(int i=0;i<output.size();i++){
			out[i] = (char)output.get(i);
		}
		return out;
	}
	
	public static void main(String[] args) {
		if(args.length == 1){
			try {
				new App(args[0]);
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		else{
			System.out.println("Usage: PdfBoxFixer <input-text-file>");
		}
	}

}
