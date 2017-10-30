using System;
using System.IO;
using System.Collections.Generic;
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
		static char[] whitespaceCharacters = { ' ', '\f', '\n', '\r', '\t', '\v' };

		public void ParseFile(string path)
		{
			int oldCursorY, newCursorY;
			lock (Console.Out)
			{
				Console.WriteLine("\tParsing " + path.Substring(path.LastIndexOf("\\")) + "... ");
				oldCursorY = Console.CursorTop - 1;
			}
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
					foreach (string s2 in split2) if (s2 != null || s2 != "") cleaned.Add(s2.ToLower());
				}
			}
			catch (Exception e)
			{
				Console.WriteLine(e.Message);
			}

			for (int i = 0; i < cleaned.Count; ++i)
			{
				Indexer.Instance.AddWordEntry(cleaned[i]);
			}

			// word link is in a seperate loop to prevent link.next_id from being null
			for (int i = 0; i < cleaned.Count; ++i)
			{
				Indexer.Instance.AddWordLink(cleaned[i], path, i > 0 ? cleaned[i - 1] : "", i < cleaned.Count - 1 ? cleaned[i + 1] : "");
			}

			lock (Console.Out)
			{
				newCursorY = Console.CursorTop;
				Console.CursorLeft = 100; Console.CursorTop = oldCursorY;
				Console.SetCursorPosition(100, oldCursorY);
				Console.Write("Done!");
				Console.CursorLeft = 0; Console.CursorTop = newCursorY;
				Console.SetCursorPosition(0, newCursorY);
			}
		}
	}
}
