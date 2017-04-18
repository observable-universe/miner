using UnityEngine;
using UnityEditor;
using System.Collections;

[InitializeOnLoad]
public class Startup {
  static Startup()
  {
    GameObject myTile = new GameObject("Empty");
    myTile.AddComponent<MeshRenderer>();
    myTile.AddComponent<MeshFilter>();
    myTile.AddComponent<Tile>();

    MeshRenderer renderer = myTile.GetComponent<MeshRenderer>();
    renderer.material.shader = Shader.Find("Specular");
    renderer.material.SetColor("_SpecColor", Color.white);
  }

  ~Startup()
  {
  }
}
