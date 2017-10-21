using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Text.RegularExpressions;

namespace Crawler
{
	public class Parser
	{
		private Parser() { }
		static Parser() { }
		private static Parser _instance = new Parser();
		public static Parser Instance { get { return _instance; } }

		Regex rgx = new Regex("[^a-zA-Z0-9 -]");

		//static string[] ValidFileTypes = { ".txt", ".html", ".c", ".cpp", ".cs", ".js", ".java" };
		static char[] whitespaceCharacters = { ' ', '\f', '\n', '\r', '\t', '\v'};

		public void ParseFile(string path)
		{
			Console.Write("\tParsing " + path.Substring(path.LastIndexOf("\\")) + "... ");
			string allText;
			string[] split = { };
			List<string> cleaned = new List<string>();

			try
			{
				allText = File.ReadAllText(path);
				split = allText.Split(whitespaceCharacters, StringSplitOptions.RemoveEmptyEntries);
				
				foreach (string s in split)
				{
					string[] split2 = rgx.Replace(s, " ").Split(whitespaceCharacters, StringSplitOptions.RemoveEmptyEntries);
					foreach (string s2 in split2) if (s2 != null || s2 != "") cleaned.Add(s2);
				}
			}
			catch
			{
			}

			foreach (string s in cleaned)
			{
				string lower = s.ToLower();
				Indexer.Instance.AddWordEntry(lower);
				Indexer.Instance.AddWordLink(lower, path);
			}

			Console.Write("Done!\n");
		}
	}
}
